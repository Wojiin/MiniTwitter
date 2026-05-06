<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $is_read = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?user $receive = null;

    #[ORM\OneToOne(inversedBy: 'notification', cascade: ['persist', 'remove'])]
    private ?Reply $reply_notif = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Message $message_notif = null;

    #[ORM\OneToOne(inversedBy: 'notification', cascade: ['persist', 'remove'])]
    private ?Post $post_notif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(bool $is_read): static
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getReceive(): ?user
    {
        return $this->receive;
    }

    public function setReceive(?user $receive): static
    {
        $this->receive = $receive;

        return $this;
    }

    public function getReplyNotif(): ?Reply
    {
        return $this->reply_notif;
    }

    public function setReplyNotif(?Reply $reply_notif): static
    {
        $this->reply_notif = $reply_notif;

        return $this;
    }

    public function getMessageNotif(): ?Message
    {
        return $this->message_notif;
    }

    public function setMessageNotif(?Message $message_notif): static
    {
        $this->message_notif = $message_notif;

        return $this;
    }

    public function getPostNotif(): ?Post
    {
        return $this->post_notif;
    }

    public function setPostNotif(?Post $post_notif): static
    {
        $this->post_notif = $post_notif;

        return $this;
    }

}
