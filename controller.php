<?php

namespace Concrete\Package\BaclucC5Crud;

defined('C5_EXECUTE') or exit(_('Access Denied.'));

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Application;
use Punic\Exception;

class Controller extends Package {
    const PACKAGE_HANDLE = 'bacluc_c5_crud';
    protected $pkgHandle = self::PACKAGE_HANDLE;
    protected $appVersionRequired = '5.7.4';
    protected $pkgVersion = '0.0.1';

    public static function getEntityManagerStatic() {
        $pkg = Package::getByHandle(self::PACKAGE_HANDLE);

        return $pkg->getEntityManager();
    }

    public function getPackageName() {
        return t('BaclucCrudPackage');
    }

    public function getPackageDescription() {
        return t('Package to provide a basic CRUD from DB to GUI');
    }

    public function getPackageAutoloaderRegistries() {
        return ['src' => 'BaclucC5Crud'];
    }

    public function install() {
        require_once $this->getPackagePath().'/vendor/autoload.php';
        $em = $this->getEntityManager();
        //begin transaction, so when block install fails, but parent::install was successfully, you don't have to uninstall the package
        $em->getConnection()->beginTransaction();

        try {
            $pkg = parent::install();
            $em = $this->getEntityManager();
            BlockType::installBlockType('bacluc_crud', $pkg);

            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();

            throw $e;
        }
    }

    public function uninstall() {
        $block = BlockType::getByHandle('bacluc_crud');
        $em = $this->getEntityManager();

        //begin transaction, so when block install fails, but parent::install was successfully, you don't have to uninstall the package
        $em->getConnection()->beginTransaction();

        try {
            if (is_object($block)) {
                $blockId = $block->getBlockTypeID();
                $application = Application::getFacadeApplication();
                $db = $application->make('database');
                //delete of blocktype not in orm way, because there is no entity BlockType
                $db->query('DELETE FROM BlockTypes WHERE btID = ?', [$blockId]);
            }
            parent::uninstall();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();

            throw $e;
        }
    }

    public function on_start() {
        require_once $this->getPackagePath().'/vendor/autoload.php';
    }
}
