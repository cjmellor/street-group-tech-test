<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Street Group - Tech Test</title>
    <script src="https://cdn-tailwindcss.vercel.app/"></script>
</head>
<body class="antialiased">
<div class="border border-red-500 flex justify-center items-center h-screen">
    <form action="{{ route('read') }}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="space-y-4">
            <div class="text-5xl font-medium font-serif bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-green-500">
                Click to choose some data
            </div>
            <div class="flex justify-between">
                <input accept="text/csv"
                       class="file:bg-pink-500 file:text-white file:px-2 file:py-1 file:border-none file:hover:bg-pink-600 file:cursor-pointer" id="csv_data"
                       name="csv_data" required type="file">
                <input class="bg-pink-500 hover:bg-pink-600 cursor-pointer text-white px-2 py-1 rounded-md font-bold tracking-wide shadow" type="submit"
                       value="Submit">
            </div>
            @error('csv_data')
            <div class="text-red-500">{{ $message }}</div>
            @enderror
        </div>
    </form>
</div>
</body>
</html>
