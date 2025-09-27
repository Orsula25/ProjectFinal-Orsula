<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\MouvementStock;
use App\Entity\Produit;
use App\Entity\Achat;
use App\Entity\Vente;
use App\Entity\Enum\TypeMouvement;

final class MouvementStockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $f = Factory::create('fr_FR');

        for ($i = 1; $i <= 20; $i++) {
            $m = new MouvementStock();

            /** @var Produit $p */
            $p = $this->getReference('produit'.rand(1,10), Produit::class);
            $m->setProduit($p);

            $qty = $f->numberBetween(1, 20);
            $type = $f->randomElement([TypeMouvement::ENTREE, TypeMouvement::SORTIE, TypeMouvement::AJUSTEMENT]);

            $m->setQuantite($qty);
            $m->setDateCreation(new \DateTimeImmutable());
            $m->setDateModification(new \DateTimeImmutable());

            // Lier soit à un achat (ENTREE) soit à une vente (SORTIE), pas les deux
            $m->setTypeMouvement($type);
            if ($type === TypeMouvement::ENTREE) {
                /** @var Achat $a */ $a = $this->getReference('achat'.rand(1,10), Achat::class);
                $m->setAchat($a);
            } elseif ($type === TypeMouvement::SORTIE) {
                /** @var Vente $v */ $v = $this->getReference('vente'.rand(1,10), Vente::class);
                $m->setVente($v);
            }


            $manager->persist($m);
            $this->addReference('mouvementStock'.$i, $m);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        // ajoute/retire selon ce que tu utilises réellement
        return [ProduitFixtures::class, AchatFixtures::class, VenteFixtures::class];
    }
}
