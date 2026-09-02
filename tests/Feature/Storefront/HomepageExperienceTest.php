<?php

test('the homepage renders the immersive storefront experience', function () {
    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('The marketplace, reimagined')
        ->assertSee('data-storefront-home', false)
        ->assertSee('data-hero-canvas', false)
        ->assertSee('Everything you want. One place.')
        ->assertSee('Shopping should feel less like searching');
});
