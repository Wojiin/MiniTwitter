<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 150)]
    private ?string $user_name = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $count_flag = null;

    #[ORM\Column]
    private ?int $delete_flag = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $last_login_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profil_picture = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $suspended_until = null;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'creator')]
    private Collection $posts;

    /**
     * @var Collection<int, Reply>
     */
    #[ORM\OneToMany(targetEntity: Reply::class, mappedBy: 'creator')]
    private Collection $replies;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_post_likes')]
    private Collection $likes;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'repostedBy')]
    #[ORM\JoinTable(name: 'user_post_reposts')]
    private Collection $repost;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'userFlag')]
    private Collection $flagPost;

    /**
     * @var Collection<int, Reply>
     */
    #[ORM\OneToMany(targetEntity: Reply::class, mappedBy: 'userFlag')]
    private Collection $flagReply;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'flagUser')]
    private ?self $userFlag = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'userFlag')]
    private Collection $flagUser;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'users')]
    private Collection $associate;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'associate')]
    private Collection $users;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $count_notification = 0;

    /**
     * @var Collection<int, Discussion>
     */
    #[ORM\ManyToMany(targetEntity: Discussion::class, mappedBy: 'discuss')]
    private Collection $discussions;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'send')]
    private Collection $messages;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->replies = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->repost = new ArrayCollection();
        $this->flagPost = new ArrayCollection();
        $this->flagReply = new ArrayCollection();
        $this->flagUser = new ArrayCollection();
        $this->associate = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->discussions = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(string $user_name): static
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCountFlag(): ?int
    {
        return $this->count_flag;
    }

    public function setCountFlag(int $count_flag): static
    {
        $this->count_flag = $count_flag;

        return $this;
    }

    public function getDeleteFlag(): ?int
    {
        return $this->delete_flag;
    }

    public function setDeleteFlag(int $delete_flag): static
    {
        $this->delete_flag = $delete_flag;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->last_login_at;
    }

    public function setLastLoginAt(\DateTimeImmutable $last_login_at): static
    {
        $this->last_login_at = $last_login_at;

        return $this;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profil_picture;
    }

    public function setProfilPicture(?string $profil_picture): static
    {
        $this->profil_picture = $profil_picture;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getSuspendedUntil(): ?\DateTimeImmutable
    {
        return $this->suspended_until;
    }

    public function setSuspendedUntil(?\DateTimeImmutable $suspended_until): static
    {
        $this->suspended_until = $suspended_until;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setCreator($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getCreator() === $this) {
                $post->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reply>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(Reply $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->setCreator($this);
        }

        return $this;
    }

    public function removeReply(Reply $reply): static
    {
        if ($this->replies->removeElement($reply)) {
            if ($reply->getCreator() === $this) {
                $reply->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Post $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }

        return $this;
    }

    public function removeLike(Post $like): static
    {
        $this->likes->removeElement($like);

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getRepost(): Collection
    {
        return $this->repost;
    }

    public function addRepost(Post $repost): static
    {
        if (!$this->repost->contains($repost)) {
            $this->repost->add($repost);
        }

        return $this;
    }

    public function removeRepost(Post $repost): static
    {
        $this->repost->removeElement($repost);

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getFlagPost(): Collection
    {
        return $this->flagPost;
    }

    public function addFlagPost(Post $flagPost): static
    {
        if (!$this->flagPost->contains($flagPost)) {
            $this->flagPost->add($flagPost);
            $flagPost->setUserFlag($this);
        }

        return $this;
    }

    public function removeFlagPost(Post $flagPost): static
    {
        if ($this->flagPost->removeElement($flagPost)) {
            if ($flagPost->getUserFlag() === $this) {
                $flagPost->setUserFlag(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reply>
     */
    public function getFlagReply(): Collection
    {
        return $this->flagReply;
    }

    public function addFlagReply(Reply $flagReply): static
    {
        if (!$this->flagReply->contains($flagReply)) {
            $this->flagReply->add($flagReply);
            $flagReply->setUserFlag($this);
        }

        return $this;
    }

    public function removeFlagReply(Reply $flagReply): static
    {
        if ($this->flagReply->removeElement($flagReply)) {
            if ($flagReply->getUserFlag() === $this) {
                $flagReply->setUserFlag(null);
            }
        }

        return $this;
    }

    public function getUserFlag(): ?self
    {
        return $this->userFlag;
    }

    public function setUserFlag(?self $userFlag): static
    {
        $this->userFlag = $userFlag;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFlagUser(): Collection
    {
        return $this->flagUser;
    }

    public function addFlagUser(self $flagUser): static
    {
        if (!$this->flagUser->contains($flagUser)) {
            $this->flagUser->add($flagUser);
            $flagUser->setUserFlag($this);
        }

        return $this;
    }

    public function removeFlagUser(self $flagUser): static
    {
        if ($this->flagUser->removeElement($flagUser)) {
            if ($flagUser->getUserFlag() === $this) {
                $flagUser->setUserFlag(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAssociate(): Collection
    {
        return $this->associate;
    }

    public function addAssociate(self $associate): static
    {
        if (!$this->associate->contains($associate)) {
            $this->associate->add($associate);
        }

        return $this;
    }

    public function removeAssociate(self $associate): static
    {
        $this->associate->removeElement($associate);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addAssociate($this);
        }

        return $this;
    }

    public function removeUser(self $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeAssociate($this);
        }

        return $this;
    }

    public function getCountNotification(): ?int
    {
        return $this->count_notification;
    }

    public function setCountNotification(int $count_notification): static
    {
        $this->count_notification = $count_notification;

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): static
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->addDiscuss($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): static
    {
        if ($this->discussions->removeElement($discussion)) {
            $discussion->removeDiscuss($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSend($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getSend() === $this) {
                $message->setSend(null);
            }
        }

        return $this;
    }
}
