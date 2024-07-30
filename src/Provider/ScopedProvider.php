<?php

namespace Siarko\ConfigFiles\Provider;

use Exception;
use Siarko\Api\State\AppState;
use Siarko\ConfigFiles\Api\ConfigPlacementStrategyInterface;
use Siarko\ConfigFiles\Api\ConfigMergerInterface;
use Siarko\ConfigFiles\Api\Modifier\ModifierManagerInterface;
use Siarko\ConfigFiles\Api\PrioritySorterInterface;
use Siarko\ConfigFiles\Api\Provider\ConfigProviderInterface;
use Siarko\Files\Api\FileInterface;
use Siarko\Files\Api\LookupInterface;
use Siarko\Files\Parse\ParserManager;

class ScopedProvider implements ConfigProviderInterface
{

    /**
     * @param LookupInterface $fileLookup
     * @param ParserManager $fileParserManager
     * @param ModifierManagerInterface $modifierManager
     * @param ConfigFileExtensionProvider $extensionProvider
     * @param AppState $appState
     * @param ConfigMergerInterface $configMerger
     * @param PrioritySorterInterface $prioritySorter
     * @param ConfigPlacementStrategyInterface $placementStrategy
     * @param string $fileParserType
     */
    public function __construct(
        private readonly LookupInterface                    $fileLookup,
        private readonly ParserManager                      $fileParserManager,
        protected readonly ConfigFileExtensionProvider      $extensionProvider,
        protected readonly AppState                         $appState,
        protected readonly ModifierManagerInterface         $modifierManager,
        protected readonly ConfigMergerInterface            $configMerger,
        protected readonly PrioritySorterInterface          $prioritySorter,
        protected readonly ConfigPlacementStrategyInterface $placementStrategy,
        protected readonly string                           $fileParserType = 'default'
    )
    {
    }

    /**
     * @param string $type
     * @return array
     * @throws Exception
     */
    public function fetch(string $type): array
    {
        $defaultScopeConfig = $this->fetchSingleScope(AppState::SCOPE_DEFAULT, $type);
        if ($this->appState->isDefaultScope()) {
            return $defaultScopeConfig;
        }
        $scopeConfig = $this->fetchSingleScope($this->appState->getAppScope(), $type);
        return $this->configMerger->merge($defaultScopeConfig, $scopeConfig);
    }

    /**
     * @param array $configs
     * @return array
     */
    protected function mergeConfigs(array $configs): array
    {
        $result = [];
        $order = $this->prioritySorter->sort($configs);
        foreach ($order as $id) {
            $result = $this->configMerger->merge($result, $configs[$id]);
        }
        return $result;
    }

    /**
     * @param string $scope
     * @param string $type
     * @return array
     * @throws Exception
     */
    private function fetchSingleScope(string $scope, string $type): array
    {
        $configs = [];
        $extension = $this->extensionProvider->getAsRegex($type);

        /** @var FileInterface $item */
        foreach ($this->fileLookup->find($type . '.' . $extension) as $item) {
            if (!$this->checkFileScope($scope, $item)) {
                continue;
            }
            $config = $this->fileParserManager->parse($item, $this->fileParserType);
            $config = $this->modifierManager->applyModifications($item, $config);
            if (empty($config)) {
                continue;
            }
            $configs = $this->placementStrategy->addConfig($type, $configs, $config);
        }

        return $this->mergeConfigs($configs);
    }

    /**
     * @param string $scope
     * @param FileInterface $item
     * @return bool
     */
    private function checkFileScope(string $scope, FileInterface $item): bool
    {
        $fileScope = basename($item->getPathInfo()->getDirname());
        if (ctype_upper($fileScope[0])) {
            $fileScope = AppState::SCOPE_DEFAULT;
        }
        return $fileScope === $scope;
    }

}