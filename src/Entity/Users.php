<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Post;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "nome", type: "string", length: 255)]
    #[Assert\NotBlank(message: "O nome não pode estar em branco.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "O nome deve ter no mínimo {{ limit }} caracteres.")]
    private ?string $nome = null;

    #[ORM\Column(name: "login", type: "string", length: 255)]
    #[Assert\NotBlank(message: "O login é obrigatório.")]
    private ?string $login = null;

    #[ORM\Column(name: "senha", type: "string", length: 255)]
    #[Assert\NotBlank(message: "A senha é obrigatória.")]
    #[Assert\Length(min: 6, minMessage: "A senha deve ter pelo menos {{ limit }} caracteres.")]
    private ?string $senha = null;

    #[ORM\Column(name: "email", type: "string", length: 255)]
    #[Assert\NotBlank(message: "O email é obrigatório.")]
    #[Assert\Email(message: "Informe um email válido.")]
    private ?string $email = null;

    #[ORM\Column(name: "avatar", type: "string", length: 255, nullable: true)]
    // #[Assert\File(
    //     maxSize: "20M",
    //     mimeTypes: ["image/jpeg", "image/png"],
    //     mimeTypesMessage: "Envie uma imagem JPEG ou PNG válida."
    // )]
    private ?string $avatar = null;

    #[ORM\Column(name: "status", type: "boolean", options: ["default" => true])]
    private ?bool $status = true;


    // POSTS
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Post::class)]
    private Collection $posts;

    // Getters e Setters abaixo
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;
        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;
        return $this;
    }

    public function getSenha(): ?string
    {
        return $this->senha;
    }

    public function setSenha(string $senha): static
    {
        $this->senha = $senha;
        return $this;
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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getUserIdentifier(): string
    {
        return $this->login; // ou email
    }

    public function getPassword(): string
    {
        return $this->senha;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER']; // você pode personalizar
    }

    public function eraseCredentials(): void
    {
        // Se tiver senha em texto temporário, limpe aqui (não obrigatório)
    }
}
