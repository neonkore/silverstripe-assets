<?php
namespace SilverStripe\Assets\Tests\FilenameParsing;

use SilverStripe\Assets\FilenameParsing\FileIDHelper;
use SilverStripe\Assets\FilenameParsing\ParsedFileID;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Assets\FilenameParsing\LegacyFileIDHelper;
use SilverStripe\Dev\Deprecation;

/**
 * All the `FileIDHelper` have the exact same signature and very similar structure. Their basic tests will share the
 * same structure.
 */
abstract class FileIDHelperTester extends SapphireTest
{

    /**
     * @return FileIDHelper
     */
    abstract protected function getHelper();

    /**
     * List of valid file IDs and their matching component. The first parameter can be use the deduc the second, and
     * the second can be used to build the first.
     * @return array
     */
    abstract public function fileIDComponents();

    /**
     * List of unclean buildFileID inputs and their expected output. Second parameter can build the first, but not the
     * other way around.
     * @return array
     */
    abstract public function dirtyFileIDComponents();

    /**
     * Similar to `dirtyFileIDComponents` only the expected output is dirty has well.
     * @return array
     */
    abstract public function dirtyFileIDFromDirtyTuple();

    /**
     * List of potentially dirty filename and their clean equivalent
     * @return array
     */
    abstract public function dirtyFilenames();

    /**
     * List of broken file ID that will break the hash parser regex.
     */
    abstract public function brokenFileID();

    /**
     * List of `fileID` and `original` parsedFileID and whatever the `fileID` is a variant of `original`
     * @return array[]
     */
    abstract public function variantOf();

    /**
     * List of parsedFieldID and a matching expected path where its variants should be search for.
     * @return array[]
     */
    abstract public function variantIn();

    /**
     * @dataProvider fileIDComponents
     * @dataProvider dirtyFileIDComponents
     */
    public function testBuildFileID($expected, $input)
    {
        $help = $this->getHelper();
        if ($help instanceof LegacyFileIDHelper && Deprecation::isEnabled()) {
            $this->markTestSkipped('Test calls deprecated code');
        }
        $this->assertEquals($expected, $help->buildFileID(...$input));
        $this->assertEquals($expected, $help->buildFileID(new ParsedFileID(...$input)));
    }

    /**
     * `buildFileID` accepts an optional `cleanFilename` argument that disables cleaning of filename.
     * @dataProvider dirtyFileIDFromDirtyTuple
     * @dataProvider fileIDComponents
     */
    public function testDirtyBuildFildID($expected, $input)
    {
        $help = $this->getHelper();
        if ($help instanceof LegacyFileIDHelper && Deprecation::isEnabled()) {
            $this->markTestSkipped('Test calls deprecated code');
        }
        $this->assertEquals($expected, $help->buildFileID(new ParsedFileID(...$input), null, null, false));
    }


    /**
     * @dataProvider dirtyFilenames
     */
    public function testCleanFilename($expected, $input)
    {
        $help = $this->getHelper();
        if ($help instanceof LegacyFileIDHelper && Deprecation::isEnabled()) {
            $this->markTestSkipped('Test calls deprecated code');
        }
        $this->assertEquals($expected, $help->cleanFilename($input));
    }

    /**
     * @dataProvider fileIDComponents
     */
    public function testParseFileID($input, $expected)
    {
        $help = $this->getHelper();
        if ($help instanceof LegacyFileIDHelper && Deprecation::isEnabled()) {
            $this->markTestSkipped('Test calls deprecated code');
        }
        $parsedFiledID = $help->parseFileID($input);

        list($expectedFilename, $expectedHash) = $expected;
        $expectedVariant = isset($expected[2]) ? $expected[2] : '';

        $this->assertNotNull($parsedFiledID);
        $this->assertEquals($input, $parsedFiledID->getFileID());
        $this->assertEquals($expectedFilename, $parsedFiledID->getFilename());
        $this->assertEquals($expectedHash, $parsedFiledID->getHash());
        $this->assertEquals($expectedVariant, $parsedFiledID->getVariant());
    }


    /**
     * @dataProvider brokenFileID
     */
    public function testParseBrokenFileID($input)
    {
        $help = $this->getHelper();
        if ($help instanceof LegacyFileIDHelper && Deprecation::isEnabled()) {
            $this->markTestSkipped('Test calls deprecated code');
        }
        $parsedFiledID = $help->parseFileID($input);
        $this->assertNull($parsedFiledID);
    }


    /**
     * @dataProvider variantOf
     */
    public function testVariantOf($variantFileID, ParsedFileID $original, $expected)
    {
        $help = $this->getHelper();
        if ($help instanceof LegacyFileIDHelper && Deprecation::isEnabled()) {
            $this->markTestSkipped('Test calls deprecated code');
        }
        $isVariantOf = $help->isVariantOf($variantFileID, $original);
        $this->assertEquals($expected, $isVariantOf);
    }

    /**
     * @dataProvider variantIn
     */
    public function testLookForVariantIn(ParsedFileID $original, $expected)
    {
        $help = $this->getHelper();
        if ($help instanceof LegacyFileIDHelper && Deprecation::isEnabled()) {
            $this->markTestSkipped('Test calls deprecated code');
        }
        $path = $help->lookForVariantIn($original);
        $this->assertEquals($expected, $path);
    }
}
