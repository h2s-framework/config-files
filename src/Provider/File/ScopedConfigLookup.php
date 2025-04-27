<?php

namespace Siarko\ConfigFiles\Provider\File;

use Siarko\Files\FileFactory;
use Siarko\Files\Lookup\DirectoryLookup;
use Siarko\Files\Lookup\ResultFactory;
use Siarko\Paths\Provider\ProjectPathProvider;

class ScopedConfigLookup extends DirectoryLookup
{
    /**
     * @param ProjectPathProvider $pathProvider
     * @param FileFactory $fileFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        ProjectPathProvider $pathProvider,
        FileFactory $fileFactory,
        ResultFactory $resultFactory
    )
    {
        parent::__construct($pathProvider, $fileFactory, $resultFactory);
    }

}