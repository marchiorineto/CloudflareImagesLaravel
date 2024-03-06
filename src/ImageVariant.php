<?php

namespace MarchioriNeto\CloudflareImagesLaravel;

class ImageVariant
{

	public string  $id;
	public bool    $alwaysPublic = false;
	public ?int    $width        = null;
	public ?int    $height       = null;
	public ?int    $blur         = null;
	public ?string $metaData     = null;
	public ?string $fit          = null;

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function validate()
	{
		if (is_null($this->width)) {
			throw new \Exception('Width is required');
		}

		if (is_null($this->height)) {
			throw new \Exception('Height is required');
		}

		if (is_null($this->metaData)) {
			throw new \Exception('Meta is required');
		}

		if (!in_array($this->metaData, ['keep', 'copyright', 'none'])) {
			throw new \Exception('Meta value must be one of keep, copyright, none');
		}

		if (is_null($this->fit)) {
			throw new \Exception('Meta is required');
		}

		if (!in_array($this->fit, ['scale-down', 'contain', 'cover', 'crop', 'pad'])) {
			throw new \Exception('Fit value must be one of scale-down, contain, cover, crop, pad');
		}
	}

	public function getOptions()
	{
		return [
			'width'    => $this->width,
			'height'   => $this->height,
			'metadata' => $this->metaData,
			'fit'      => $this->fit,
			'blur'     => $this->blur,
		];
	}

	/**
	 * @param string $id
	 * @return ImageVariant
	 */
	public function setId(string $id): ImageVariant
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @param bool $alwaysPublic
	 * @return ImageVariant
	 */
	public function alwaysPublic(bool $alwaysPublic): ImageVariant
	{
		$this->alwaysPublic = $alwaysPublic;

		return $this;
	}

	/**
	 * @param int|null $width
	 * @return ImageVariant
	 */
	public function width(?int $width): ImageVariant
	{
		$this->width = $width;

		return $this;
	}

	/**
	 * @param int|null $height
	 * @return ImageVariant
	 */
	public function height(?int $height): ImageVariant
	{
		$this->height = $height;

		return $this;
	}

	/**
	 * @param int|null $blur
	 * @return ImageVariant
	 */
	public function blur(?int $blur): ImageVariant
	{
		if ($blur < 0 || $blur > 100) {
			throw new \Exception('Blur must be between 0 and 100');
		}

		$this->blur = $blur;

		return $this;
	}

	/**
	 * @param string|null $metaData
	 * @return ImageVariant
	 */
	public function metaData(?string $metaData): ImageVariant
	{
		$this->metaData = $metaData;

		return $this;
	}

	/**
	 * @param string|null $fit
	 * @return ImageVariant
	 */
	public function fit(?string $fit): ImageVariant
	{
		$this->fit = $fit;

		return $this;
	}

}
