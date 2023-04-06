<?php declare(strict_types=1);

namespace InteractionDesignFoundation\NovaUnlayerField\Tests;

use Illuminate\Database\Eloquent\Model;
use InteractionDesignFoundation\NovaUnlayerField\Unlayer;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

/** @covers \InteractionDesignFoundation\NovaUnlayerField\Unlayer */
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
    public function it_properly_runs_saving_callback(): void
    {
        $inMemoryModel = new class extends Model {
            public string $design = '';
            public string $html = '';
        };
        $field = Unlayer::make('Design', 'design')
            ->savingCallback(static function (NovaRequest $request, $attribute, Model $model, $outputHtmlFieldName) {
                $model->html = $request->input($outputHtmlFieldName);
            });

        $this->emulateNovaUpdateRequestForSingleField($field, 'design', $inMemoryModel, [
            'design' => '{}',
            'design_html' => '<p>Hello!</p>', // automatically added field. Pattern: [original field name] + _html (in this case "design" + "_html")
        ]);

        $this->assertSame('<p>Hello!</p>', $inMemoryModel->html);
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

        $field->config(static function () {
            return ['projectId' => 'XXX'];
        });

        $this->assertArrayHasKey('projectId', $field->meta()['config']);
        $this->assertSame('XXX', $field->meta()['config']['projectId']);
    }

    /**
     * White box testing of Nova field needed to unsure
     * that custom functionality [savingCallback() method] works as expected.
     * @param array<string, mixed> $resourceUpdateRequestData
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    private function emulateNovaUpdateRequestForSingleField(
        Field $field,
        string $fieldAttributeName,
        Model $model,
        array $resourceUpdateRequestData
    ): void {
        $request = NovaRequest::create('', 'POST', $resourceUpdateRequestData);
        $field->fillInto($request, $model, $fieldAttributeName);
    }
}
