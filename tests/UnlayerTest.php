<?php declare(strict_types=1);

namespace InteractionDesignFoundation\NovaUnlayerField\Tests;

use InteractionDesignFoundation\NovaUnlayerField\Unlayer;

final class UnlayerTest extends TestCase
{
    /** @test */
    public function it_resolves_callback_to_html_code(): void
    {
        $field = new Unlayer('any_name');

        $field->html(static fn (): string => '<p>Hello!</p>');

        $this->assertSame('<p>Hello!</p>', $field->meta()['html'] ?? null);
    }

    /** @test */
    public function it_accepts_array_as_config(): void
    {
        $field = new Unlayer('any_name');

        $field->config(['projectId' => 'XXX']);

        $this->assertArrayHasKey('projectId', $field->meta()['config']);
        $this->assertSame('XXX', $field->meta()['config']['projectId']);
    }

    /** @test */
    public function it_accepts_callable_as_config(): void
    {
        $field = new Unlayer('any_name');

        $field->config(function () {
            return ['projectId' => 'XXX'];
        });

        $this->assertArrayHasKey('projectId', $field->meta()['config']);
        $this->assertSame('XXX', $field->meta()['config']['projectId']);
    }
}
