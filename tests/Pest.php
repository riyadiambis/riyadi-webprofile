<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
 | Pest dipakai hanya sebagai smoke test, lihat bagian "Aturan testing"
 | di CLAUDE.md. Tidak ada unit test dan tidak ada test logika bisnis.
 */

pest()->extend(TestCase::class)->in('Feature');

pest()->use(RefreshDatabase::class)->in('Feature/RutePublikTest.php');
