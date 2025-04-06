<?php

namespace App\Http\Controllers\Api;

use App\Dtos\Account\TransactionDto;
use App\Enums\Account\TransactionType;
use App\Http\Requests\Api\Auth\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Transaction;
use App\Processes\Account\DepositProcess;
use App\Processes\Account\Errors\TransactionException;
use App\Processes\Account\WithdrawProcess;
use App\Queries\TransactionsQuery;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends ApiController
{
    public function index(Account $account): AnonymousResourceCollection
    {
        Gate::authorize('view-account-transactions', [Transaction::class, $account]);

        return TransactionResource::collection(TransactionsQuery::for($account)->paginate());
    }

    public function store(TransactionRequest $request, Account $account, DepositProcess $depositProcess, WithdrawProcess $withdrawProcess)
    {
        Gate::authorize('create', [Transaction::class, $account]);

        try {
            $transactionType = $request->enum('type', TransactionType::class);

            $transaction = new TransactionDto($account->id, money($request->amount), $transactionType);

            if ($transactionType === TransactionType::Deposit) {
                $transaction = $depositProcess->run($transaction);
            } elseif ($transactionType === TransactionType::Withdraw) {
                $transaction = $withdrawProcess->run($transaction);
            }

            return (new TransactionResource($transaction->transaction))->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (TransactionException $e) {
            return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
