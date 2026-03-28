<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Support\TransactionDetailPayload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type', 'all');
        // Sanitize filter: unknown type values fall back to "all" instead of odd partial listings.
        if (! in_array($type, ['all', 'deposit', 'withdrawal'], true)) {
            $type = 'all';
        }

        $q = trim((string) $request->query('q', ''));
        $barSerial = trim((string) $request->query('bar_serial', ''));

        $deposits = collect();
        $withdrawals = collect();

        // When filtering "withdrawal"-only, skip deposit query entirely (and vice versa) for clarity and less work.
        if ($type !== 'withdrawal') {
            $depositQuery = Deposit::query()
                ->with(['account.customer', 'metalType', 'allocatedBar'])
                ->latest();
            $this->applyDepositFilters($depositQuery, $q, $barSerial);
            $deposits = $depositQuery->get();
        }

        if ($type !== 'deposit') {
            $withdrawalQuery = Withdrawal::query()
                ->with(['account.customer', 'metalType', 'allocatedBars'])
                ->latest();
            $this->applyWithdrawalFilters($withdrawalQuery, $q, $barSerial);
            $withdrawals = $withdrawalQuery->get();
        }

        // sort_ts is Unix time so deposits and withdrawals merge in true chronological order.
        $rows = $deposits
            ->map(fn (Deposit $deposit) => $this->mapDepositRow($deposit))
            ->concat($withdrawals->map(fn (Withdrawal $withdrawal) => $this->mapWithdrawalRow($withdrawal)))
            ->sortByDesc(fn (array $row): int => $row['sort_ts'])
            ->values();

        return view('transactions.index', [
            'transactions' => $rows,
            'filters' => [
                'type' => $type,
                'q' => $q,
                'bar_serial' => $barSerial,
            ],
        ]);
    }

    private function applyDepositFilters(Builder $query, string $q, string $barSerial): void
    {
        if ($q !== '') {
            $pattern = $this->caseInsensitiveLike($q);
            $query->where(function (Builder $w) use ($pattern): void {
                $w->whereRaw('LOWER(deposit_number) LIKE ?', [$pattern])
                    ->orWhereHas('account', function (Builder $acc) use ($pattern): void {
                        $acc->whereRaw('LOWER(account_number) LIKE ?', [$pattern])
                            ->orWhereHas('customer', function (Builder $c) use ($pattern): void {
                                $c->whereRaw('LOWER(full_name) LIKE ?', [$pattern]);
                            });
                    })
                    ->orWhereHas('metalType', function (Builder $m) use ($pattern): void {
                        $m->whereRaw('LOWER(name) LIKE ?', [$pattern]);
                    });
            });
        }

        if ($barSerial !== '') {
            $pattern = $this->caseInsensitiveLike($barSerial);
            // Bar serial filter on deposits uses hasMany allocatedBar; only allocated flows create bar rows.
            $query->whereHas('allocatedBar', function (Builder $b) use ($pattern): void {
                $b->whereRaw('LOWER(serial_number) LIKE ?', [$pattern]);
            });
        }
    }

    private function applyWithdrawalFilters(Builder $query, string $q, string $barSerial): void
    {
        if ($q !== '') {
            $pattern = $this->caseInsensitiveLike($q);
            $query->where(function (Builder $w) use ($pattern): void {
                $w->whereRaw('LOWER(withdrawal_number) LIKE ?', [$pattern])
                    ->orWhereHas('account', function (Builder $acc) use ($pattern): void {
                        $acc->whereRaw('LOWER(account_number) LIKE ?', [$pattern])
                            ->orWhereHas('customer', function (Builder $c) use ($pattern): void {
                                $c->whereRaw('LOWER(full_name) LIKE ?', [$pattern]);
                            });
                    })
                    ->orWhereHas('metalType', function (Builder $m) use ($pattern): void {
                        $m->whereRaw('LOWER(name) LIKE ?', [$pattern]);
                    });
            });
        }

        if ($barSerial !== '') {
            $pattern = $this->caseInsensitiveLike($barSerial);
            // Withdrawals find bars through allocatedBars (many bars can be released in one withdrawal).
            $query->whereHas('allocatedBars', function (Builder $b) use ($pattern): void {
                $b->whereRaw('LOWER(serial_number) LIKE ?', [$pattern]);
            });
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function mapDepositRow(Deposit $deposit): array
    {
        $customer = $deposit->account->customer;
        $createdAt = $deposit->created_at;

        return [
            'sort_ts' => $createdAt->timestamp,
            'account_summary' => $customer->full_name.' — '.$deposit->account->account_number,
            'metal' => $deposit->metalType->name,
            'storage_type' => $deposit->storage_type,
            'quantity_kg' => (float) $deposit->quantity_kg,
            'is_deposit' => true,
            'type_label' => 'Deposit',
            'date_display' => $createdAt->format('M d, Y'),
            'detail' => TransactionDetailPayload::forDeposit($deposit),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapWithdrawalRow(Withdrawal $withdrawal): array
    {
        $customer = $withdrawal->account->customer;
        $createdAt = $withdrawal->created_at;

        return [
            'sort_ts' => $createdAt->timestamp,
            'account_summary' => $customer->full_name.' — '.$withdrawal->account->account_number,
            'metal' => $withdrawal->metalType->name,
            'storage_type' => $withdrawal->storage_type,
            'quantity_kg' => (float) $withdrawal->quantity_kg,
            'is_deposit' => false,
            'type_label' => 'Withdrawal',
            'date_display' => $createdAt->format('M d, Y'),
            'detail' => TransactionDetailPayload::forWithdrawal($withdrawal),
        ];
    }
}
