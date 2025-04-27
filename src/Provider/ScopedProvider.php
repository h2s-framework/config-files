<?php

namespace Siarko\ConfigFiles\Provider;

use Exception;
use Siarko\Api\State\AppState;
use Siarko\ConfigFiles\Api\ConfigPlacementStrategyInterface;
use Siarko\ConfigFiles\Api\ConfigMergerInterface;
use Siarko\ConfigFiles\Api\Modifier\ModifierManagerInterface;
use Siarko\ConfigFiles\Api\PrioritySorterInterface;
use Siarko\ConfigFiles\Api\Provider\ConfigFileNameProviderInterface;
use Siarko\ConfigFiles\Api\Provider\ConfigProviderInterface;
use Siarko\ConfigFiles\Api\Provider\LookupScopeComparatorInterface;
use Siarko\Files\Api\LookupInterface;
use Siarko\Files\Parse\ParserManager;

class ScopedProvider implements ConfigProviderInterface
{

    /**
     * @param LookupInterface $fileLookup
     * @param ParserManager $fileParserManager
     * @param ConfigFileExtensionProvider $extensionProvider
     * @param AppState $appState
     * @param ModifierManagerInterface $modifierManager
     * @param ConfigMergerInterface $configMerger
     * @param PrioritySorterInterface $prioritySorter
     * @param ConfigPlacementStrategyInterface $placementStrategy
     * @param ConfigFileNameProviderInterface $configFileNameProvider
     * @param LookupScopeComparatorInterface $lookupScopeComparator
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
        protected readonly ConfigFileNameProviderInterface  $configFileNameProvider,
        protected readonly LookupScopeComparatorInterface   $lookupScopeComparator,
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
        $fileName = $this->configFileNameProvider->getFileName($type);
        foreach ($this->fileLookup->find($fileName . '.' . $extension) as $item) {
            if (!$this->lookupScopeComparator->compare($scope, $item)) {
                continue;
            }
            $file = $item->getFile();
            $config = $this->fileParserManager->parse($file, $this->fileParserType);
            $config = $this->modifierManager->applyModifications($file, $config);
            if (empty($config)) {
                continue;
            }
            $configs = $this->placementStrategy->addConfig($type, $configs, $config);
        }

        return $this->mergeConfigs($configs);
    }

}