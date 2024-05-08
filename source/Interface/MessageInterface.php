<?php

namespace Source\Interface;

interface MessageInterface
{
    public function __toString(): string;
    public function getText(): string;
    public function getType(): string;
    public function dispatch(string $type, string $message): self;
    public function render(): string;
    public function json(): string;
    public function flash(): void;
}