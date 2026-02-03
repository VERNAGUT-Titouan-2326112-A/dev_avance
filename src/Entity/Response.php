<?php

namespace App\Entity;

use App\Repository\ResponseRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Response - Représente une réponse possible à une question QCM
 *
 * Cette entité gère les réponses (correctes ou incorrectes) associées à chaque QCM.
 * Elle contient un libellé (description) et l'indicateur de correction.
 * Elle expose une API REST complète pour les opérations CRUD.
 */
#[ORM\Entity(repositoryClass: ResponseRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(description: 'Récupère la liste de toutes les réponses'),
        new Get(description: 'Récupère une réponse spécifique par son ID'),
        new Post(description: 'Crée une nouvelle réponse'),
        new Put(description: 'Met à jour une réponse existante'),
        new Delete(description: 'Supprime une réponse'),
    ]
)]
class Response
{
    /**
     * Identifiant unique de la réponse
     * Clé primaire auto-générée par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Libellé/description de la réponse
     * Chaîne de caractères limité à 255 caractères
     * Exemple: "Paris", "Madrid", "Londres" pour une question sur les capitales
     */
    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * Indicateur de correction de la réponse
     * Chaîne de caractères limité à 255 caractères
     * Valeurs typiques: "correct", "incorrect", "correct", "partiellement correct", etc.
     */
    #[ORM\Column(length: 255)]
    private ?string $response = null;

    /**
     * Référence au QCM auquel cette réponse appartient
     * Relation "plusieurs-à-un" : plusieurs réponses peuvent appartenir à un même QCM
     * L'attribut inversedBy indique que QCM gère la relation inverse
     *
     * @var ?QCM
     */
    #[ORM\ManyToOne(inversedBy: 'responses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QCM $qcm = null;

    /**
     * Récupère l'identifiant unique de la réponse
     *
     * @return ?int L'ID de la réponse, null si non encore persistée
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le libellé/description de la réponse
     *
     * @return ?string Le libellé de la réponse
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * Définit le libellé/description de la réponse
     *
     * @param string $libelle Le libellé à assigner à la réponse
     * @return static Instance courante pour permettre l'appel en chaîne (fluent interface)
     */
    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Récupère l'indicateur de correction de la réponse
     *
     * @return ?string L'indicateur de correction
     */
    public function getResponse(): ?string
    {
        return $this->response;
    }

    /**
     * Définit l'indicateur de correction de la réponse
     *
     * @param string $response L'indicateur de correction à assigner
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setResponse(string $response): static
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Récupère le QCM auquel cette réponse appartient
     *
     * @return ?QCM Le QCM parent, null si non associé
     */
    public function getQcm(): ?QCM
    {
        return $this->qcm;
    }

    /**
     * Définit le QCM auquel cette réponse appartient
     *
     * @param ?QCM $qcm Le QCM parent à assigner, null pour dissocier
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setQcm(?QCM $qcm): static
    {
        $this->qcm = $qcm;

        return $this;
    }
}
