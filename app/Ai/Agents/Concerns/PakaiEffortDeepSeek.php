<?php

namespace App\Ai\Agents\Concerns;

use Laravel\Ai\Enums\Lab;

/** Thinking mode + reasoning_effort DeepSeek; provider lain tidak dikirimi apa-apa. */
trait PakaiEffortDeepSeek
{
    public function providerOptions(Lab|string $provider): array
    {
        if (($provider instanceof Lab ? $provider->value : $provider) !== Lab::DeepSeek->value) {
            return [];
        }

        return [
            'thinking' => ['type' => 'enabled'],
            'reasoning_effort' => config('ai.providers.deepseek.reasoning_effort'),
        ];
    }
}
