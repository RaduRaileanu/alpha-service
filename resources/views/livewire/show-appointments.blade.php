<div>

    <div>
        <!-- Filters -->
        <div class="filters mb-4 flex space-x-4">
            <div>
                <select wire:model="service_id" class="border p-2 rounded">
                    <option value="">All Services</option>
                    @foreach ($services as $service)
                        <option value={{ $service->id }}>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model="status" class="border p-2 rounded">
                    <option value="">All Statuses</option>
                    <option value="received">Received</option>
                    <option value="registered">Registered</option>
                    <option value="finalized">Finalized</option>
                </select>
            </div>
            <div style="color:white">
                <input wire:model="date" type="date" class="border p-2 rounded" />
            </div>
        </div>
    
        <!-- Appointment Table -->
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Service Name</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Client Name</th>
                    <th class="border px-4 py-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($appointments as $appointment)
                    <tr>
                        <td class="border px-4 py-2">{{ $appointment->service->name }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($appointment->status) }}</td>
                        <td class="border px-4 py-2">{{ $appointment->user->name }}</td>
                        <td class="border px-4 py-2">{{ $appointment->date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    
        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $appointments->links() }}
        </div>
    </div>
    
</div>
