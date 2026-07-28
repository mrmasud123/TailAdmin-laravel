@extends('layouts.app')

@section('content')

    <x-common.page-breadcrumb :pageTitle="[
        ['name' => 'Purchase Returns', 'link' => route('admin.purchase-returns.manage')],
        ['name' => 'Create', 'link' => '#'],
    ]" />

    <div class="bg-white dark:bg-white/5 rounded-2xl shadow-lg p-6"
         x-data="{
            rows: [
                { batch: 'NP-2607 — Napa Extra 500mg', available: 40, qty: 5, cost: 850 },
                { batch: 'SC-1188 — Seclo 20mg', available: 22, qty: 3, cost: 620 },
            ],
            total() { return this.rows.reduce((sum, r) => sum + (r.qty * r.cost), 0).toLocaleString() }
         }">

        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">New Purchase Return</h2>

        <form>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Goods Receipt (GRN)</label>
                    <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                        <option>GRN-0230 — Square Pharmaceuticals</option>
                        <option>GRN-0227 — Beximco Pharma</option>
                        <option>GRN-0221 — ACI Limited</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Return Date</label>
                    <x-form.date-picker
                        id="return_date"
                        name="return_date"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
                        required
                        placeholder="Select date"
                    />
                </div>
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Reason</label>
                    <select class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm">
                        <option>Damaged in transit</option>
                        <option>Wrong item shipped</option>
                        <option>Near expiry on arrival</option>
                        <option>Quantity mismatch</option>
                    </select>
                </div>
            </div>

            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Batches to Return</h3>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Batch</th>
                            <th class="px-4 py-3 text-center">Available Qty</th>
                            <th class="px-4 py-3 text-center">Return Qty</th>
                            <th class="px-4 py-3 text-right">Unit Cost</th>
                            <th class="px-4 py-3 text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <template x-for="(row, i) in rows" :key="i">
                            <tr>
                                <td class="px-4 py-2.5 text-gray-800 dark:text-gray-100" x-text="row.batch"></td>
                                <td class="px-4 py-2.5 text-center text-gray-500 dark:text-gray-400" x-text="row.available"></td>
                                <td class="px-4 py-2.5">
                                    <input type="number" min="0" :max="row.available" x-model.number="row.qty"
                                           class="w-24 mx-auto block px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm text-center">
                                </td>
                                <td class="px-4 py-2.5 text-right text-gray-600 dark:text-gray-300" x-text="'৳ ' + row.cost"></td>
                                <td class="px-4 py-2.5 text-right font-medium text-gray-700 dark:text-gray-200" x-text="'৳ ' + (row.qty * row.cost).toLocaleString()"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-300">Notes</label>
                    <textarea rows="4" placeholder="Additional context for the supplier or accounts team..."
                              class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm"></textarea>
                </div>
                <div class="flex flex-col items-end justify-end">
                    <div class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between font-semibold text-gray-800 dark:text-white text-base border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span>Total Return Value</span>
                            <span x-text="'৳ ' + total()"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 border-t border-gray-200 dark:border-gray-700 pt-5">
                <a href="{{ route('admin.purchase-returns.manage') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow">
                    Submit Return
                </button>
            </div>
        </form>
    </div>

@endsection
