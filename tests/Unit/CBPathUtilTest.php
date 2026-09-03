<?php

namespace CrudBooster\Tests\Unit;

use CrudBooster\Helpers\CBPathUtil;
use Orchestra\Testbench\TestCase;

class CBPathUtilTest extends TestCase {
    public function test_returns_default_cms_path_when_no_path_provided() {
        $this->assertEquals('cms', CBPathUtil::getCmsPath());
    }

    public function test_returns_custom_cms_path_when_no_path_provided() {
        config(['cb.admin_path' => 'admin']);
        $this->assertEquals('admin', CBPathUtil::getCmsPath());
    }

    public function test_returns_cms_path_with_provided_path() {
        $this->assertEquals('cms/some/path', CBPathUtil::getCmsPath('some/path'));
    }

    public function test_returns_custom_cms_path_with_provided_path() {
        config(['cb.admin_path' => 'admin']);
        $this->assertEquals('admin/some/path', CBPathUtil::getCmsPath('some/path'));
    }

    public function test_trims_slashes_from_provided_path() {
        $this->assertEquals('cms/some/path', CBPathUtil::getCmsPath('/some/path/'));
    }
}
