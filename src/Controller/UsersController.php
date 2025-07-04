<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Users;
use App\Form\UsersType;

use Doctrine\ORM\EntityManagerInterface;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UsersController extends AbstractController
{
    #[Route('/usuarios-count', name: 'usuarios_count')]
    public function usuarios_count(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Exemplo: contar registros da tabela User
        $total = $em->getRepository(Users::class)->count([]);

        return new Response("Total de usuários: $total");
    }

    #[Route('/usuarios', name: 'usuarios')]
    public function listar(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $q = $request->query->get('q');

        $qb = $em->getRepository(Users::class)->createQueryBuilder('u')
            ->leftJoin('u.posts', 'p')
            ->addSelect('COUNT(p.id) as HIDDEN posts_count');

        if ($q) {
            $qb->where('LOWER(u.nome) LIKE :q')
                ->setParameter('q', '%' . strtolower($q) . '%');
        }

        // Ordenação dinâmica (por nome, email ou posts_count)
        $sort = $request->query->get('sort', 'u.nome');
        $direction = $request->query->get('direction', 'asc');

        $validSorts = ['u.nome', 'u.email', 'posts_count'];
        if (in_array($sort, $validSorts)) {
            if ($sort === 'posts_count') {
                $qb->orderBy('posts_count', $direction);
            } else {
                $qb->orderBy($sort, $direction);
            }
        } else {
            $qb->orderBy('u.nome', 'asc'); // Padrão se o sort não for válido
        }

        // Agrupar por usuário para a contagem funcionar
        $qb->groupBy('u.id');

        // Paginação
        $pagination = $paginator->paginate(
            $qb,                           // Query
            $request->query->getInt('page', 1), // Página atual
            10                             // Itens por página
        );

        return $this->render('usuarios/lista.html.twig', [
            'usuarios' => $pagination,
        ]);
    }

    #[Route('/usuarios/novo', name: 'usuarios_novo')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        FileUploader $uploader,
        ValidatorInterface $validator
    ): Response {
        $usuario = new Users();
        $form = $this->createForm(UsersType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $validator->validate($usuario);
            if (count($errors) > 0) {
                return $this->render('usuarios/novo.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                ]);
            }

            $avatarFile = $form->get('avatar')->getData();

            if ($avatarFile) {
                $filename = $uploader->upload($avatarFile);
                $usuario->setAvatar($filename);
            }

            $senhaHasheada = $passwordHasher->hashPassword($usuario, $usuario->getSenha());
            $usuario->setSenha($senhaHasheada);

            $em->persist($usuario);
            $em->flush();

            return $this->redirectToRoute('usuarios');
        }

        return $this->render('usuarios/novo.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/upload', name: 'upload')]
    public function upload(Request $request, FileUploader $uploader)
    {
        $usuario = new Users();
        $form = $this->createForm(UsersType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('avatar')->getData();

            if ($file) {
                $filename = $uploader->upload($file);
                $usuario->setAvatar($filename);
            }

            // You need to inject EntityManagerInterface $em as an argument to this method
            // and persist the user, or handle accordingly.
            // Example:
            // $em->persist($usuario);
            // $em->flush();
        }

        return $this->render('usuarios/novo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // #[Route('/usuarios/validar-manualmente', name: 'usuarios_valida')]
    // public function validarManual(Request $request, ValidatorInterface $validator, EntityManagerInterface $em): Response
    // {
    //      $this->denyAccessUnlessGranted('ROLE_USER');
    //     $user = new Users();
    //     $user->setNome(''); // inválido (NotBlank)
    //     $user->setLogin('admin');
    //     $user->setSenha('123'); // inválido (Length min: 6)
    //     $user->setEmail('email-invalido'); // inválido (Email)

    //     // Valida o objeto inteiro
    //     $errors = $validator->validate($user);

    //     if (count($errors) > 0) {
    //         // Retorna os erros como string ou JSON
    //         return new Response((string) $errors, 400);
    //     }

    //     // Se não tiver erros, salva normalmente
    //     $em->persist($user);
    //     $em->flush();

    //     return new Response('Usuário salvo com sucesso!');
    // }
}
