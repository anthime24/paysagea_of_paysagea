<?php

namespace App\Core\Service;

use App\Core\Entity\Composition;
use App\Core\Entity\CompositionEntite;
use App\Core\Entity\CompositionVue;
use App\Core\Entity\CreationEntite;
use App\Core\Entity\Entite;
use App\Core\Entity\EntitePhoto;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use ReflectionMethod;

class CopyService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function copy($newObject, $object, $manytoones, $excludeClasses)
    {
        foreach ((array)$object as $k => $v) {
            $vars = explode('_', str_replace($this->em->getClassMetadata(get_class($object))->rootEntityName, '', $k));
            $item = '';

            foreach ($vars as $s) {
                $item .= ucfirst(trim(strtolower($s)));
            }

            $getter = 'get' . $item;
            $setter = 'set' . $item;
            $adder = 'add' . $item;
            $remover = 'remove' . $item;

            if (method_exists($newObject, $setter)) {
                $newObject->$setter($object->$getter());
            } else {
                if (method_exists($newObject, $getter) && $object->$getter() instanceof PersistentCollection) {
                    if (!method_exists($newObject, $adder) && substr($adder, -1) == 's') {
                        $adder = substr($adder, 0, -1);
                    }

                    if (method_exists($newObject, $adder)) {
                        //foreach ($newObject->$getter() as $i)
                        //    $newObject->$remover($i);

                        foreach ($object->$getter() as $i) {
                            $class = get_class($i);
                            $linker = 'set' . str_replace('App\\Core\\Entity\\', '', get_class($newObject));

                            $isExclude = false;
                            foreach ($excludeClasses as $excludeClassInstance) {
                                if ($i instanceof $excludeClassInstance) {
                                    $isExclude = true;
                                }
                            }

                            if ($isExclude) {
                                continue;
                            }

                            $isInstance = false;
                            foreach ($manytoones as $instance) {
                                if ($i instanceof $instance) {
                                    $isInstance = true;
                                }
                            }

                            if ($isInstance) {
                                $newI = $this->copy(new $class(), $i, [], $excludeClasses);
                                $this->em->persist($newI);
                                $newI->$linker($newObject);
                                $newObject->$adder($newI);

                                if (method_exists($newI, 'cloneFile')) {
                                    $newI->cloneFile($i);
                                }
                            } else {
                                $newObject->$adder($i);
                            }
                        }
                    }
                }
            }
        }

        return $newObject;
    }

    public function copyEntite($object)
    {
        $newEntite = $this->copy(new Entite(), $object, [new EntitePhoto()], [new CreationEntite(), new CompositionEntite()]);
        $newEntite->setNom('Copie de "' . $newEntite->getNom() . '"');

        if (count($object->getCategories()) > 0) {
            $newEntite->getCategories()->clear();
            foreach ($object->getCategories() as $category) {
                $newEntite->addCategory($category);
            }
        }

        $this->em->persist($newEntite);
        $this->em->flush();

        foreach ($newEntite->getEntitePhotos() as $entitePhoto) {
            $entitePhoto->moveUpload();
        }

        $reflectorMethod = new ReflectionMethod(Entite::class, 'getUploadRootDir');
        $reflectorMethod->setAccessible(true);
        $oldUploadDir = $reflectorMethod->invoke($object);
        $newUploadDir = $reflectorMethod->invoke($newEntite);

        if ($object->getLassoPhoto() !== null && trim($object->getLassoPhoto()) != "") {
            if (!file_exists($newUploadDir)) {
                mkdir($newUploadDir, 0777, true);
            }

            copy($oldUploadDir . '/' . $object->getLassoPhoto(), $newUploadDir . '/' . $object->getLassoPhoto());
        }

        return $newEntite;
    }

    public function copyComposition($object)
    {
        $entite = $object->getEntite();
        $newComposition = $this->copy(new Composition(), $object, [new CompositionEntite(), new CompositionVue()], [new CreationEntite()]);
        $newComposition->setNom('Copie de "' . $newComposition->getNom() . '"');
        $entite->setComposition($object);
        $object->setEntite($entite);
        $newComposition->setEntite(null);

        $this->em->persist($entite);
        $this->em->persist($newComposition);
        $this->em->flush();

        $newEntite = $this->copy(new Entite(), $entite, [new EntitePhoto()], [new CreationEntite(), new CompositionEntite()]);
        $newEntite->setAcronyme('Composition ' . $newComposition->getId());
        $newEntite->setNom('Copie de "' . $newEntite->getNom() . '"');
        $newEntite->setComposition($newComposition);
        $newComposition->setEntite($newEntite);

        $this->em->persist($newEntite);
        $this->em->flush();

        $newComposition->generateImages();
        $this->em->flush();

        return $newComposition;
    }

}
