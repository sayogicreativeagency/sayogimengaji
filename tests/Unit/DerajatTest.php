<?php

use App\Enums\Derajat;
use App\Enums\Kitab;

it('memetakan penilaian ulama ke derajat ringkas', function (Kitab $kitab, array $grades, ?Derajat $hasil) {
    expect(Derajat::dariPenilaian($kitab, $grades))->toBe($hasil);
})->with([
    'bukhari tanpa penilaian' => [Kitab::Bukhari, [], Derajat::Shahih],
    'muslim abaikan penilaian' => [Kitab::Muslim, [['name' => 'X', 'grade' => "Da'if"]], Derajat::Shahih],
    'hasan sahih = shahih' => [Kitab::Tirmidhi, [['name' => 'Al-Albani', 'grade' => 'Hasan Sahih']], Derajat::Shahih],
    'hasan' => [Kitab::AbuDawud, [['name' => 'Al-Albani', 'grade' => 'Hasan']], Derajat::Hasan],
    "da'if" => [Kitab::AbuDawud, [['name' => 'Al-Albani', 'grade' => "Da'if"]], Derajat::Dhaif],
    'daif tanpa apostrof' => [Kitab::Nasai, [['name' => 'Al-Albani', 'grade' => 'Daif Jiddan']], Derajat::Dhaif],
    'maudu' => [Kitab::IbnuMajah, [['name' => 'Al-Albani', 'grade' => "Mawdu'"]], Derajat::Maudhu],
    'albani diutamakan' => [Kitab::AbuDawud, [
        ['name' => 'Zubair Ali Zai', 'grade' => 'Isnaad Hasan'],
        ['name' => 'Al-Albani', 'grade' => "Da'if"],
    ], Derajat::Dhaif],
    'tanpa albani pakai yang pertama' => [Kitab::Malik, [
        ['name' => 'Shuaib Al Arnaut', 'grade' => 'Sahih Lighairihi'],
        ['name' => 'Lain', 'grade' => "Da'if"],
    ], Derajat::Shahih],
    'maudu ejaan lain' => [Kitab::Tirmidhi, [['name' => 'Al-Albani', 'grade' => 'Maudu']], Derajat::Maudhu],
    'munkar = dhaif' => [Kitab::Tirmidhi, [['name' => 'Al-Albani', 'grade' => 'Munkar']], Derajat::Dhaif],
    'shadh = dhaif' => [Kitab::Tirmidhi, [['name' => 'Al-Albani', 'grade' => 'Shadh']], Derajat::Dhaif],
    'batil = maudhu' => [Kitab::Tirmidhi, [['name' => 'Al-Albani', 'grade' => 'Batil']], Derajat::Maudhu],
    'tanpa penilaian' => [Kitab::AbuDawud, [], null],
    'tidak dikenal' => [Kitab::AbuDawud, [['name' => 'Al-Albani', 'grade' => 'Unknown']], null],
]);
