<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingUploaded extends Notification
{
    use Queueable;

    protected $training;
    protected $coachName;
    protected $categoryName;

    /**
     * Create a new notification instance.
     */
    public function __construct($training, $coachName, $categoryName)
    {
        $this->training = $training;
        $this->coachName = $coachName;
        $this->categoryName = $categoryName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'training_id' => $this->training->id,
            'coach_name' => $this->coachName,
            'category_name' => $this->categoryName,
            'file_path_pdf' => $this->training->file_path_pdf,
            'message' => "{$this->coachName} subió una nueva planificación para la categoría {$this->categoryName}.",
            'action_url' => $this->training->file_path_pdf,
        ];
    }
}
