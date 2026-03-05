<?php

declare(strict_types=1);

namespace App\Http\Services\Firebase;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Http\HttpClientOptions;
use Kreait\Laravel\Firebase\FirebaseProject;
use Kreait\Laravel\Firebase\FirebaseProjectManager as BaseFirebaseProjectManager;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

class FirebaseProjectManager extends BaseFirebaseProjectManager
{
    protected function configure(string $name): FirebaseProject
    {
        $factory = $this->app->make(Factory::class);

        $config = $this->configuration($name);

        if ($tenantId = $config['auth']['tenant_id'] ?? null) {
            $factory = $factory->withTenantId($tenantId);
        }

        $credentials = $config['credentials']['file'] ?? ($config['credentials'] ?? null);
        if ($credentials) {
            if (is_string($credentials)) {
                $credentials = $this->resolveJsonCredentials($credentials);
                if (! str_starts_with($credentials, '{') && ! is_file($credentials)) {
                    throw new InvalidArgumentException(
                        'Firebase credentials file not found: '.$credentials.'. Set FIREBASE_CREDENTIALS in .env to the path of your service account JSON (e.g. app/Firebase/firebase_credentials.txt).'
                    );
                }
            }

            $factory = $factory->withServiceAccount($credentials);
        } else {
            throw new InvalidArgumentException(
                'Firebase credentials are required. Set FIREBASE_CREDENTIALS in .env to the path of your service account JSON (e.g. app/Firebase/firebase_credentials.txt), or place the file at app/Firebase/firebase_credentials.txt.'
            );
        }

        if ($projectId = $config['project_id'] ?? null) {
            $factory = $factory->withProjectId($projectId);
        }

        if ($databaseUrl = $config['database']['url'] ?? null) {
            $factory = $factory->withDatabaseUri($databaseUrl);
        }

        if ($authVariableOverride = $config['database']['auth_variable_override'] ?? null) {
            $factory = $factory->withDatabaseAuthVariableOverride($authVariableOverride);
        }

        if ($defaultStorageBucket = $config['storage']['default_bucket'] ?? null) {
            $factory = $factory->withDefaultStorageBucket($defaultStorageBucket);
        }

        if ($cacheStore = $config['cache_store'] ?? null) {
            $cache = $this->app->make('cache')->store($cacheStore);

            if ($cache instanceof CacheInterface) {
                $cache = new Psr16Adapter($cache);
            } else {
                throw new InvalidArgumentException('The cache store must be an instance of a PSR-6 or PSR-16 cache');
            }

            $factory = $factory
                ->withVerifierCache($cache)
                ->withAuthTokenCache($cache);
        }

        if ($logChannel = $config['logging']['http_log_channel'] ?? null) {
            $factory = $factory->withHttpLogger(
                $this->app->make('log')->channel($logChannel)
            );
        }

        if ($logChannel = $config['logging']['http_debug_log_channel'] ?? null) {
            $factory = $factory->withHttpDebugLogger(
                $this->app->make('log')->channel($logChannel)
            );
        }

        $options = HttpClientOptions::default();

        if ($proxy = $config['http_client_options']['proxy'] ?? null) {
            $options = $options->withProxy($proxy);
        }

        if ($timeout = $config['http_client_options']['timeout'] ?? null) {
            $options = $options->withTimeOut((float) $timeout);
        }

        if ($middlewares = $config['http_client_options']['guzzle_middlewares'] ?? null) {
            $options = $options->withGuzzleMiddlewares($middlewares);
        }

        $factory = $factory->withHttpClientOptions($options);

        return new FirebaseProject(
            $factory,
            $config['firestore']['database'] ?? null,
        );
    }
}
