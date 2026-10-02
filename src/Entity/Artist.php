<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

// Une ligne de la table `artists` = un objet Artist
#[ORM\Entity]
#[ORM\Table(name: 'artists')]
class Artist implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idArtist', type: 'integer')]
    private ?int $idArtist = null;

    #[ORM\Column(name: 'Name', type: 'string', length: 45, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'Annee', type: 'integer', nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(name: 'Description', type: 'string', length: 45, nullable: true)]
    private ?string $description = null;

    public function __construct(?int $idArtist = null, ?string $name = null, ?int $annee = null, ?string $description = null)
    {
        $this->idArtist = $idArtist;
        $this->name = $name;
        $this->annee = $annee;
        $this->description = $description;
    }

    // Ligne SQL → objet (utilisé par le Repository)
    public static function fromArray(array $row): self
    {
        return new self(
            isset($row['idArtist']) ? (int) $row['idArtist'] : null,
            $row['Name'] ?? null,
            isset($row['Annee']) ? (int) $row['Annee'] : null,
            $row['Description'] ?? null,
        );
    }

    // Objet → tableau
    public function toArray(): array
    {
        return [
            'idArtist'    => $this->idArtist,
            'Name'        => $this->name,
            'Annee'       => $this->annee,
            'Description' => $this->description,
        ];
    }

    // Pour que json_encode($artist) fonctionne directement
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getId(): ?int { return $this->idArtist; }
    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): self { $this->name = $name; return $this; }
    public function getAnnee(): ?int { return $this->annee; }
    public function setAnnee(?int $annee): self { $this->annee = $annee; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
}