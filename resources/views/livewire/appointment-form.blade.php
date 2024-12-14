
<div>
    <!-- Service Selection -->
    <div>
        <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
        <select wire:model="selectedService" id="service" name="service" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="">-- Select a Service --</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}">{{ $service->name }}</option>
            @endforeach
        </select>
        @error('selectedService') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <!-- Vehicle Section -->
    <div class="space-y-4">
        @if (count($vehicles))
            <div>
                <label for="vehicle" class="block text-sm font-medium text-gray-700">Vehicle</label>
                <select wire:model="selectedVehicle" id="vehicle" name='vehicle' class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">-- Select a Vehicle --</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->brand }} {{ $vehicle->model }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="button" wire:click="$set('showNewVehicleForm', true)" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">New Vehicle</button>
            </div>
        @endif

        @if (!count($vehicles) || $showNewVehicleForm)
            <div class="space-y-4">
                <h4 class="text-lg font-medium text-gray-800">Create New Vehicle</h4>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select wire:model="vehicleType" name="vehicle-type" id="vehicle" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">-- Select a vehicle type --</option>
                        @foreach (['van', 'hatchback', 'coupe', 'break', 'SUV'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('brand') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                    <input type="text" wire:model="brand" name="brand" id="brand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    @error('brand') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                    <input type="text" wire:model="model" name="model" id="model" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    @error('model') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="chassisSeries" class="block text-sm font-medium text-gray-700">Chassis Series</label>
                    <input type="text" wire:model="chassisSeries" name="chassis-series" id="chassisSeries" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    @error('chassisSeries') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="manufacturingYear" class="block text-sm font-medium text-gray-700">Manufacturing Year</label>
                    <input type="number" wire:model="manufacturingYear" name="manufacturing-year" id="manufacturingYear" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    @error('manufacturingYear') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="engine" class="block text-sm font-medium text-gray-700">Engine</label>
                    <select wire:model="engine" name="engine" id="engine" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">-- Select an engine type --</option>
                        @foreach (['petrol', 'diesel', 'hybrid', 'electric', 'lng'] as $engine)
                            <option value="{{ $engine }}">{{ $engine }}</option>
                        @endforeach
                    </select>
                    @error('engine') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>
        @endif
    </div>

    <!-- Appointment Type -->
    <div>
        <label for="appointmentType" class="block text-sm font-medium text-gray-700">Appointment Type</label>
        <select wire:model="appointmentType" name="appointment-type" id="appointmentType" wire:change="calculateCost" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="">-- Select Appointment Type --</option>
            <option value="itp">ITP</option>
            <option value="repairs">Service Repairs</option>
        </select>
        @error('appointmentType') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <h4 class="text-lg font-medium text-gray-800">Appointment time</h4>

    <!-- Date -->
    <div>
        <label for="date" class="block text-sm font-medium text-gray-700">Select appointment date</label>
        <input type="date" wire:model="date" wire:change="getTimeSlots" name="date" id="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
        @error('date') 
            <span class="text-sm text-red-600">{{ $message }}</span> 
        @enderror
    </div>

    {{ $selectedTimeSlot }}

    <!-- Time Slot -->
    <div>
        <select wire:model="selectedTimeSlot" id="service" name="time-slot" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="">-- Select a time slot --</option>
            @foreach ($timeSlots as $timeSlot)
                <option value="{{ $timeSlot }}">{{ $timeSlot }}</option>
            @endforeach
        </select>
        @error('timeSlot') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <!-- Observations -->
    <div>
        <label for="observations" class="block text-sm font-medium text-gray-700">Observations</label>
        <textarea wire:model="observations" name="observations" id="observations" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
    </div>

    <!-- Total Cost -->
    @if($appointmentType == 'itp')
        <div>
            <p class="text-lg font-medium text-gray-800">Total Cost: {{ $cost }}</p>
        </div>
    @endif

</div>
