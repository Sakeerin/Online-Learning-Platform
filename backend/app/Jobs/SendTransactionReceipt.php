<?php

namespace App\Jobs;

use App\Mail\TransactionReceiptMail;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTransactionReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Transaction $transaction
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('Sending transaction receipt', [
            'transaction_id' => $this->transaction->id,
            'user_id' => $this->transaction->user_id,
            'amount' => $this->transaction->amount,
        ]);

        Mail::to($this->transaction->user)->send(new TransactionReceiptMail($this->transaction));
    }
}

