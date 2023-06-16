<?php

namespace App\Adventure;

/**
 * Represents the inventory of the player in the adventure game.
 */
class Inventory
{
    /**
     * @var array The inventory of the player.
     */
    private $inventory = [];

    /**
     * Retrieves the entire inventory.
     *
     * @return array The inventory.
     */
    public function getInventory(): array
    {
        return $this->inventory;
    }

    /**
     * Retrieves the list of items in the inventory.
     *
     * @return array The names of the items in the inventory.
     */
    public function lookInInventory(): array
    {
        return array_keys($this->inventory);
    }

    /**
     * Retrieves the specified item from the inventory.
     *
     * @param string $itemName The name of the item to retrieve.
     * @return Item|null The item object if found, or null if not found.
     */
    public function getItem(string $itemName): ?Item
    {
        if ($this->hasItem($itemName)) {
            return $this->inventory[$itemName];
        }

        return null;
    }

    /**
     * Checks if the inventory contains the specified item.
     *
     * @param string $itemName The name of the item to check.
     * @return bool True if the item is present, false otherwise.
     */
    public function hasItem(string $itemName): bool
    {
        return isset($this->inventory[$itemName]);
    }

    /**
     * Adds an item to the inventory.
     *
     * @param Item $item The item to add.
     */
    public function addItem(Item $item): void
    {
        $this->inventory[$item->getName()] = $item;
    }

    /**
     * Removes an item from the inventory.
     *
     * @param Item $item The item to remove.
     */
    public function removeItem(Item $item): void
    {
        $key = array_search($item, $this->inventory);
        if ($key !== false) {
            unset($this->inventory[$key]);
        }
    }
}
