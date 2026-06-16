<?php

it('returns a successful response', function () {
    // A rota '/' redireciona para '/login' — comportamento esperado do SolidariSys
    $response = $this->get('/');

    $response->assertRedirect('/login');
});