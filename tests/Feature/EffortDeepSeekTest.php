<?php

use App\Ai\Agents\PemilihHadist;
use App\Ai\Agents\PengembangKataKunci;
use Laravel\Ai\Enums\Lab;

it('mengirim thinking + reasoning_effort hanya ke deepseek', function (string $agent) {
    config(['ai.providers.deepseek.reasoning_effort' => 'medium']);

    expect((new $agent)->providerOptions('deepseek'))->toBe(['thinking' => ['type' => 'enabled'], 'reasoning_effort' => 'medium'])
        ->and((new $agent)->providerOptions(Lab::DeepSeek))->toHaveKey('reasoning_effort', 'medium')
        ->and((new $agent)->providerOptions('openrouter'))->toBe([]);
})->with([PengembangKataKunci::class, PemilihHadist::class]);
