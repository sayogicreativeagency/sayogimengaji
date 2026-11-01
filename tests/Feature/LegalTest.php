<?php

it('menampilkan syarat & ketentuan', function () {
    $this->get('/syarat-ketentuan')->assertOk()
        ->assertSee('Syarat &amp; Ketentuan', false)
        ->assertSee('bukan fatwa')
        ->assertSee('tidak membuka akses')
        ->assertSee('href="/kebijakan-privasi"', false);
});

it('menampilkan kebijakan privasi yang menyebut pihak ketiga dan data yang dikumpulkan', function () {
    $this->get('/kebijakan-privasi')->assertOk()
        ->assertSeeInOrder(['Kebijakan Privasi', 'progres', 'DeepSeek', 'Tiongkok', 'Open-Meteo', 'BigDataCloud', 'equran.id', 'Quran.com'])
        ->assertSee('alamat IP')
        ->assertSee('UU No. 27 Tahun 2022')
        ->assertSee('href="/kontak"', false);
});

it('menampilkan sumber dan atribusi', function () {
    $this->get('/sumber')->assertOk()
        ->assertSee('href="https://github.com/fawazahmed0/hadith-api"', false)
        ->assertSee('href="https://equran.id"', false)
        ->assertSee('href="https://quran.com"', false)
        ->assertSee('Weather data by Open-Meteo.com')
        ->assertSee('CC BY 4.0')
        ->assertSee('adhan')
        ->assertSee('SIL Open Font License');
});

it('menautkan halaman legal dari footer dan form daftar', function () {
    $this->get('/')->assertOk()->assertSeeInOrder(['href="/syarat-ketentuan"', 'href="/kebijakan-privasi"', 'href="/sumber"'], false);
    $this->get('/daftar')->assertOk()->assertSee('Dengan mendaftar, kamu menyetujui');
});

it('mencantumkan atribusi Open-Meteo di widget cuaca', function () {
    $this->get('/')->assertOk()->assertSee('href="https://open-meteo.com/"', false);
});
