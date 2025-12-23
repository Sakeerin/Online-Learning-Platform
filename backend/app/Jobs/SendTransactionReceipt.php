<?php

namespace App\Jobs;

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
        // Send email receipt to user
        // For now, we'll just log it
        // In production, create a Mailable class and send email
        
        \Log::info('Transaction receipt sent', [
            'transaction_id' => $this->transaction->id,
            'user_id' => $this->transaction->user_id,
            'amount' => $this->transaction->amount,
        ]);

        // TODO: Implement email sending
        // Mail::to($this->transaction->user->email)
        //     ->send(new TransactionReceipt($this->transaction));
    }
}

