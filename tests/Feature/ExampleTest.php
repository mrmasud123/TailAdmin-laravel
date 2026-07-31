<?php

test('guest is redirected to sso silent check', function () {
    $response = $this->get('/');

//    $response->assertRedirect('https://hospital.test/sso/silent-check');
});
