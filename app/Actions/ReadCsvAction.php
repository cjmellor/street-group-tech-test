<?php

namespace App\Actions;

use Illuminate\Support\Str;
use League\Csv\Reader;

class ReadCsvAction
{
    public function handle($request): array
    {
        $csvData = Reader::createFromPath(
            path: $request->file('csv_data'),
            open_mode: 'r+'
        )->setHeaderOffset(offset: 0);

        $homeOwners = collect($csvData)->values()->flatten();

        $person = [];

        /**
         * Rows where an owner has the format:
         * - {title} {first_name} {last_name}
         */
        $singleHomeOwners = $homeOwners
            // We don't want a row where there are these words/symbols in it
            ->filter(fn($homeOwner): bool => (! Str::contains(haystack: $homeOwner, needles: ['and', '&', '.'])))
            // If the first name is only an initial
            ->reject(function ($homeOwner): bool {
                $first_name = explode(separator: ' ', string: $homeOwner)[1];

                return Str::length($first_name) <= 1;
            })
            ->map(function ($singleHomeOwner) use ($person): array {
                [$title, $first_name, $last_name] = explode(separator: ' ', string: $singleHomeOwner);

                // A first name initial might be suffixed with a period. This will look for it and remove it.
                if (Str::contains(haystack: $singleHomeOwner, needles: '.')) {
                    $first_name = Str::remove(search: '.', subject: $first_name);
                }

                // Map the data
                $person['title'] = $title;
                $person['first_name'] = $first_name;
                $person['initial'] = $initial ?? null;
                $person['$last_name'] = $last_name;

                return $person;
            })->toArray();

        /**
         * Rows where an owner has the format:
         * - {title} {initial}.? {last_name}
         */
        $homeOwnerWithFirstNameInitial = $homeOwners
            // Don't want rows with '&'
            ->filter(fn($firstInitial): bool => (! Str::contains(haystack: $firstInitial, needles: ['&'])))
            // A row might have a period suffix, so have to count that as a character
            ->filter(function ($firstInitial): bool {
                $initial = explode(separator: ' ', string: $firstInitial)[1];

                return Str::length($initial) <= 2;
            })
            ->map(function ($firstInitial) use ($person): array {
                [$title, $first_name, $last_name] = explode(separator: ' ', string: $firstInitial);

                $person['title'] = $title;

                // If the first_name *isn't* an initial
                if (Str::length($first_name) > 1) {
                    $person['first_name'] = $first_name;
                    $person['initial'] = null;
                }

                // else, if it is
                if (Str::length($first_name) <= 2) {
                    $person['first_name'] = null;
                    $person['initial'] = Str::remove('.', $first_name);
                }

                $person['$last_name'] = $last_name;

                return $person;
            })->toArray();

        /**
         * Rows where an owner has the format:
         * - {title} {last_name}
         */

        $multipleOwners = $homeOwners
            ->filter(fn($owner): bool => (Str::contains(haystack: $owner, needles: ['and', '&'])))
            // Honestly, just did this to make it easier...
            ->map(fn($owner): string => Str::replace(search: '&', replace: 'and', subject: $owner))
            ->map(fn($owner): array => explode(separator: ' and ', string: $owner))
            ->map(function ($owner) use ($person): array {
                $dataSetTwo = explode(' ', $owner[1]);

                if (count($dataSetTwo) == 2) {
                    $person['title'] = $dataSetTwo[0];
                    $person['first_name'] = null;
                    $person['initial'] = null;
                    $person['last_name'] = $dataSetTwo[1];
                }

                if (count($dataSetTwo) > 2) {
                    $person['title'] = $dataSetTwo[0];
                    $person['first_name'] = $dataSetTwo[1];
                    $person['initial'] = null;
                    $person['last_name'] = $dataSetTwo[2];
                }

                return $person;
            })
            ->toArray();

        return array_merge($singleHomeOwners, $homeOwnerWithFirstNameInitial, $multipleOwners);
    }
}
