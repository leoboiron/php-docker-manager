<?php

namespace PhpDockerManager;

use PhpDockerManager\Case\StatusCase;
use PhpDockerManager\Container\Config;
use PhpDockerManager\Container\Instance;
use PhpDockerManager\Exception\UncreatableContainerException;
use PhpDockerManager\Exception\UndownloadableImageException;
use PhpDockerManager\Exception\UnjoinableContainerException;
use PhpDockerManager\Exception\UnremovableContainerException;
use PhpDockerManager\Exception\UnstartableContainerException;
use Symfony\Component\HttpClient\CurlHttpClient;

class Manager
{
    private CurlHttpClient $httpClient;

    public function __construct(string $apiPath)
    {
        if (str_starts_with($apiPath, 'unix://')) {
            $this->httpClient = new CurlHttpClient([
                'base_uri' => 'http://localhost',
                'headers' => ['Accept' => 'application/json'],
                'bindto' => str_replace('unix://', '', $apiPath),
            ]);

            return;
        }

        throw new \InvalidArgumentException('Only Unix sockets are supported at the moment.');
    }

    public function run(Config $config): Instance
    {
        if ($this->isImageAvailable($config->getImage(), $config->getTag()) === false) {
            $this->downloadImage($config->getImage(), $config->getTag());
        }

        $instance = $this->create($config);
        $this->start($instance);

        return $instance;
    }

    public function create(Config $config): Instance
    {
        $nameParam = $config->getName() ? '?name=' . $config->getName() : null;

        $json = ['Image' => $config->getImage() . ':' . $config->getTag()];

        if (!empty($config->getName())) {
            $json['Name'] = $config->getName();
        }

        if (!empty($config->getLabels())) {
            $json['Labels'] = $config->getLabels();
        }

        if (!empty($config->getEnvVars())) {
            $json['Env'] = array_map(
                fn($key, $value) => $key . '=' . $value,
                array_keys($config->getEnvVars()),
                $config->getEnvVars()
            );
        }

        if (!empty($config->getNetwork())) {
            $json['HostConfig'] = ['NetworkMode' => $config->getNetwork()];
        }

        $response = $this->httpClient->request('POST', "/containers/create$nameParam", ['json' => $json]);
        if ($response->getStatusCode() !== 201) {
            throw new UncreatableContainerException($response->getContent(false));
        }

        return $this->getInstanceFromId($response->toArray()['Id']);
    }

    public function start(Instance $instance): void
    {
        $id = $instance->getId();
        $response = $this->httpClient->request('POST',"/containers/$id/start");

        if ($response->getStatusCode() !== 204) {
            throw new UnstartableContainerException($response->getContent(false));
        }
    }

    public function getStatus(Instance $instance): StatusCase
    {
        $id = $instance->getId();
        $response = $this->httpClient->request('GET', "/containers/$id/json");

        if ($response->getStatusCode() !== 200) {
            throw new UnjoinableContainerException($response->getContent(false));
        }

        $data = $response->toArray();
        $status = $data['State']['Status'];

        return match ($status) {
            'created' => StatusCase::CREATED,
            'running' => StatusCase::RUNNING,
            'paused' => StatusCase::PAUSED,
            'restarting' => StatusCase::RESTARTING,
            'exited' => StatusCase::EXITED,
            'dead' => StatusCase::DEAD,
            default => throw new \LogicException('Unknown status: ' . $status),
        };
    }

    public function isRunning(Instance $instance): bool
    {
        return $this->getStatus($instance) === StatusCase::RUNNING;
    }

    public function isImageAvailable(string $image, string $tag): bool
    {
        return $this->httpClient->request('GET', "/images/$image:$tag/json")->getStatusCode() === 200;
    }

    public function downloadImage(string $image, string $tag): void
    {
        $response = $this->httpClient->request('POST', "/images/create?fromImage=$image&tag=$tag");

        if ($response->getStatusCode() !== 200) {
            throw new UndownloadableImageException($response->getContent(false));
        }
    }

    public function getInstanceFromId(string $id): Instance
    {
        $response = $this->httpClient->request('GET', "/containers/$id/json");
        if ($response->getStatusCode() !== 200) {
            throw new UnjoinableContainerException($response->getContent(false));
        }
        $data = $response->toArray();

        $config = new Config(
            explode(':', $data['Config']['Image'])[0],
            explode(':', $data['Config']['Image'])[1]
        );

        $config
            ->setName($data['Name'])
            ->setNetwork($data['HostConfig']['NetworkMode'])
        ;

        foreach ($data['Config']['Labels'] as $key => $value) {
            $config->addLabel($key, $value);
        }

        foreach ($data['Config']['Env'] as $envVar) {
            [$key, $value] = explode('=', $envVar);
            $config->addEnvVar($key, $value);
        }

        return new Instance($config, $id);
    }

    public function remove(Instance $instance): void
    {
        $id = $instance->getId();
        $response = $this->httpClient->request('DELETE', "/containers/$id?force=true&v=true");

        if ($response->getStatusCode() !== 204) {
            throw new UnremovableContainerException($response->getContent(false));
        }
    }
}
