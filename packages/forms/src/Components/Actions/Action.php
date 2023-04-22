<?php

namespace Filament\Forms\Components\Actions;

use Filament\Actions\Concerns\HasMountableArguments;
use Filament\Actions\MountableAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;

class Action extends MountableAction
{
    use Concerns\BelongsToComponent;
    use HasMountableArguments;

    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();
    }

    public function getLivewireCallActionName(): string
    {
        return 'callMountedFormComponentAction';
    }

    public function getLivewireMountAction(): ?string
    {
        if (! $this->isMountedOnClick()) {
            return null;
        }

        if ($this->getUrl()) {
            return null;
        }

        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
            $argumentsParameter .= '';
        }

        return "mountFormComponentAction('{$this->getComponent()->getKey()}', '{$this->getName()}'{$argumentsParameter})";
    }

    public function toFormComponent(): ActionContainer
    {
        return ActionContainer::make($this);
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'component' => [$this->getComponent()],
            'context', 'operation' => [$this->getComponent()->getContainer()->getOperation()],
            'get' => [$this->getComponent()->getGetCallback()],
            'model' => [$this->getComponent()->getModel()],
            'record' => [$this->getComponent()->getRecord()],
            'set' => [$this->getComponent()->getSetCallback()],
            'state' => [$this->getComponent()->getState()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getComponent()->getRecord();

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
