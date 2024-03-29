<?php

declare(strict_types=1);

namespace DataType\Value;

/**
 * Class OrderElement
 *
 * Represents a single piece of ordering
 *
 * e.g.
 * "+name" => order by name ascending.
 * "-date" => order by date descending.
 */
class OrderElement
{
    private string $name;

    private string $order;

    /**
     * OrderElement constructor.
     * @param string $name
     * @param string $order
     */
    public function __construct(string $name, string $order)
    {
        $this->name = $name;
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }
}
