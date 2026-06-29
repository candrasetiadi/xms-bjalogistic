<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('company', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        $clients = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function show(Client $client)
    {
        return redirect()->route('clients.edit', $client);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:150',
            'company' => 'nullable|string|max:150',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:100',
            'addr'    => 'nullable|string',
            'city'    => 'nullable|string|max:100',
            'dest'    => 'nullable|string|max:100',
            'note'    => 'nullable|string',
        ]);

        $data['id'] = (int)(microtime(true) * 1000);

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client berhasil ditambahkan.');
    }

    public function storeQuick(Request $request)
    {
        $request->validate(['name' => 'required|string|max:150']);

        $client = Client::create([
            'id'      => (int)(microtime(true) * 1000),
            'name'    => $request->name,
            'company' => $request->company ?? '',
            'phone'   => $request->phone ?? '',
            'email'   => $request->email ?? '',
            'addr'    => $request->addr ?? '',
            'city'    => $request->city ?? '',
            'dest'    => '',
        ]);

        return response()->json([
            'id'      => $client->id,
            'name'    => $client->name,
            'company' => $client->company,
            'label'   => $client->name . ($client->company ? ' / ' . $client->company : ''),
        ]);
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:150',
            'company' => 'nullable|string|max:150',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:100',
            'addr'    => 'nullable|string',
            'city'    => 'nullable|string|max:100',
            'dest'    => 'nullable|string|max:100',
            'note'    => 'nullable|string',
        ]);

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Client berhasil diupdate.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $clients = Client::where('name', 'like', "%$q%")
            ->orWhere('company', 'like', "%$q%")
            ->limit(10)
            ->get(['id', 'name', 'company', 'phone', 'city', 'dest', 'addr']);

        return response()->json($clients);
    }
}
