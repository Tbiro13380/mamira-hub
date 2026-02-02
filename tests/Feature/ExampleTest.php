<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    // A rota home redireciona para login quando não autenticado
    $response->assertRedirect(route('login'));
});