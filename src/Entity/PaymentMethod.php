<?php

namespace App\Entity;

use App\Repository\PaymentMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PaymentMethodRepository::class)
 */
class PaymentMethod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"order", "paymentMethod"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"order", "paymentMethod"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"order", "paymentMethod"})
     */
    private $discount;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="paymentMethod")
     * @Groups({"order_detail"})
     */
    private $orders;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"order", "paymentMethod"})
     */
    private $sendPaymentEmail;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setPaymentMethod($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getPaymentMethod() === $this) {
                $order->setPaymentMethod(null);
            }
        }

        return $this;
    }

    public function getSendPaymentEmail(): ?bool
    {
        return $this->sendPaymentEmail;
    }

    public function setSendPaymentEmail(?bool $sendPaymentEmail): self
    {
        $this->sendPaymentEmail = $sendPaymentEmail;

        return $this;
    }
}
