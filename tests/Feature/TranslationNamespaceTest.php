<?php

namespace CrudBooster\Tests\Feature;

use CrudBooster\Tests\BaseTestCase;

class TranslationNamespaceTest extends BaseTestCase
{
    /** @test */
    public function api_builder_translations_resolve_to_real_text()
    {
        $this->assertSame('API Infrastructure', trans('api_builder::api_builder.list.title'));
        $this->assertNotSame('api_builder::api_builder.list.title', trans('api_builder::api_builder.list.title'));
    }

    /** @test */
    public function audit_log_translations_resolve_to_real_text()
    {
        $this->assertSame('Audit Log', trans('audit_log::audit_log.title'));
        $this->assertNotSame('audit_log::audit_log.title', trans('audit_log::audit_log.title'));
    }

    /** @test */
    public function api_builder_and_audit_log_namespaces_do_not_collide()
    {
        // Regression test: both providers previously registered under the shared
        // 'cb' translation namespace, so the second boot() call silently overwrote
        // the first namespace's path, leaving one module's keys unresolved.
        $namespaces = app('translator')->getLoader()->namespaces();

        $this->assertArrayHasKey('api_builder', $namespaces);
        $this->assertArrayHasKey('audit_log', $namespaces);
        $this->assertNotSame($namespaces['api_builder'], $namespaces['audit_log']);
    }
}
