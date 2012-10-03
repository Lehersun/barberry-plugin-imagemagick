<?php
namespace Barberry\Plugin\Imagemagic;
use Barberry\Plugin;
use Barberry\Direction;
use Barberry\ContentType;
use Composer\Script\Event;

class Installer implements Plugin\InterfaceInstaller {

    public static function postPackageInstall(Event $event) {
        $event->getIo()->write(
            print_r(
                $event->getOperation()->getPackage(),
                true
            )
        );
    }

    /**
     * @var string
     */
    private $tempDirectory;

    public function __construct($tempDirectory) {
        $this->tempDirectory = $tempDirectory;
    }

    public function install(Direction\ComposerInterface $composer) {
        foreach (self::directions() as $pair) {
            $composer->writeClassDeclaration(
                $pair[0],
                eval('return ' .$pair[1] . ';'),
                <<<PHP
new Plugin_Imagemagic_Converter ($pair[1], '{$this->tempDirectory}');
PHP
                ,
                'new Plugin_Imagemagic_Command'
            );
        }
    }

//--------------------------------------------------------------------------------------------------

    private static function directions() {
        return array(
            array(ContentType::jpeg(), 'ContentType::gif()'),
            array(ContentType::jpeg(), 'ContentType::png()'),
            array(ContentType::jpeg(), 'ContentType::jpeg()'),

            array(ContentType::gif(), 'ContentType::jpeg()'),
            array(ContentType::gif(), 'ContentType::png()'),
            array(ContentType::gif(), 'ContentType::gif()'),

            array(ContentType::png(), 'ContentType::jpeg()'),
            array(ContentType::png(), 'ContentType::gif()'),
            array(ContentType::png(), 'ContentType::png()'),
        );
    }
}
