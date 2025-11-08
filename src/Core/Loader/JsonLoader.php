<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Velkuns\GameTextEngine\Core\Loader;

use Velkuns\GameTextEngine\Exception\Core\LoaderException;

readonly class JsonLoader
{
    /**
     * @return array<mixed>
     * @throws LoaderException
     */
    public function fromFile(string $filePathname): array
    {
        if (!\file_exists($filePathname)) {
            throw new LoaderException('File not found: ' . $filePathname, 1200);
        }

        if (!\is_readable($filePathname)) {
            throw new LoaderException('File is not readable: ' . $filePathname, 1201); // @codeCoverageIgnore
        }

        $json = \file_get_contents($filePathname);

        if ($json === false) {
            throw new LoaderException('Error reading file: ' . $filePathname, 1202); // @codeCoverageIgnore
        }

        return $this->fromString($json);
    }

    /**
     * @return array<mixed>
     * @throws LoaderException
     */
    public function fromString(string $json): array
    {
        try {
            /** @var array<mixed> $data */
            $data = \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new LoaderException('Content is not valid JSON (error: ' . $exception->getMessage() . ')', 1203);
        }

        return $data;
    }
}
