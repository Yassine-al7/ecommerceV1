<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold mb-4">Mes Factures</h1>

        @if($invoices->isEmpty())
            <p>Aucune facture disponible.</p>
        @else
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">#</th>
                        <th class="py-2 px-4 border-b">Date</th>
                        <th class="py-2 px-4 border-b">Montant</th>
                        <th class="py-2 px-4 border-b">Statut</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $invoice->id }}</td>
                            <td class="py-2 px-4 border-b">{{ $invoice->created_at->format('d/m/Y') }}</td>
                            <td class="py-2 px-4 border-b">{{ number_format((float)$invoice->amount, 2, ',', ' ') }} €</td>
                            <td class="py-2 px-4 border-b">{{ $invoice->status }}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="{{ route('seller.invoices.show', $invoice->id) }}" class="text-blue-500">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @endsection
</body>
</html>
