<?php

namespace App\Http\Controllers;

use App\Actions\ReadCsvAction;
use App\Http\Requests\ReadCsvRequest;

class ReadCsvController extends Controller
{
    public function __invoke(ReadCsvRequest $request)
    {
        $csvData = app(ReadCsvAction::class)
            ->handle($request);

        dump($csvData);
    }
}
