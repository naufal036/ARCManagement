<?php

it('redirects root url to login screen', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
