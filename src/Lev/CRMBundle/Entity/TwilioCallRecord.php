<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TwilioCallRecord
 *
 * @ORM\Table(name="twilio_call_record", indexes={@ORM\Index(name="IDX_72801445166D1F9C", columns={"project_id"}), @ORM\Index(name="IDX_728014454A7E4868", columns={"sale_id"}), @ORM\Index(name="IDX_728014459395C3F3", columns={"customer_id"}), @ORM\Index(name="IDX_72801445DE12AB56", columns={"created_by"}), @ORM\Index(name="IDX_72801445E5B533F9", columns={"appointment_id"}), @ORM\Index(name="IDX_72801445FFA0C224", columns={"office_id"})})
 * @ORM\Entity
 */
class TwilioCallRecord
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="office_id", type="integer", nullable=false)
     */
    private $officeId;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer", nullable=false)
     */
    private $customerId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="appointment_id", type="integer", nullable=true)
     */
    private $appointmentId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="callstatus", type="string", length=15, nullable=false)
     */
    private $callstatus;

    /**
     * @var string|null
     *
     * @ORM\Column(name="recording_url", type="string", length=1000, nullable=true)
     */
    private $recordingUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="recording_sid", type="string", length=34, nullable=true)
     */
    private $recordingSid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="recording_duration", type="integer", nullable=true)
     */
    private $recordingDuration;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="callback_source", type="string", length=50, nullable=true)
     */
    private $callbackSource;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sequence_number", type="integer", nullable=true)
     */
    private $sequenceNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="call_sid", type="string", length=50, nullable=false)
     */
    private $callSid;

    /**
     * @var string
     *
     * @ORM\Column(name="from_number", type="string", length=100, nullable=false)
     */
    private $fromNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="to_number", type="string", length=100, nullable=false)
     */
    private $toNumber;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sale_id", type="integer", nullable=true)
     */
    private $saleId;

    /**
     * @var \Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfficeId(): ?int
    {
        return $this->officeId;
    }

    public function setOfficeId(int $officeId): self
    {
        $this->officeId = $officeId;

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getAppointmentId(): ?int
    {
        return $this->appointmentId;
    }

    public function setAppointmentId(?int $appointmentId): self
    {
        $this->appointmentId = $appointmentId;

        return $this;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?int $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCallstatus(): ?string
    {
        return $this->callstatus;
    }

    public function setCallstatus(string $callstatus): self
    {
        $this->callstatus = $callstatus;

        return $this;
    }

    public function getRecordingUrl(): ?string
    {
        return $this->recordingUrl;
    }

    public function setRecordingUrl(?string $recordingUrl): self
    {
        $this->recordingUrl = $recordingUrl;

        return $this;
    }

    public function getRecordingSid(): ?string
    {
        return $this->recordingSid;
    }

    public function setRecordingSid(?string $recordingSid): self
    {
        $this->recordingSid = $recordingSid;

        return $this;
    }

    public function getRecordingDuration(): ?int
    {
        return $this->recordingDuration;
    }

    public function setRecordingDuration(?int $recordingDuration): self
    {
        $this->recordingDuration = $recordingDuration;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getCallbackSource(): ?string
    {
        return $this->callbackSource;
    }

    public function setCallbackSource(?string $callbackSource): self
    {
        $this->callbackSource = $callbackSource;

        return $this;
    }

    public function getSequenceNumber(): ?int
    {
        return $this->sequenceNumber;
    }

    public function setSequenceNumber(?int $sequenceNumber): self
    {
        $this->sequenceNumber = $sequenceNumber;

        return $this;
    }

    public function getCallSid(): ?string
    {
        return $this->callSid;
    }

    public function setCallSid(string $callSid): self
    {
        $this->callSid = $callSid;

        return $this;
    }

    public function getFromNumber(): ?string
    {
        return $this->fromNumber;
    }

    public function setFromNumber(string $fromNumber): self
    {
        $this->fromNumber = $fromNumber;

        return $this;
    }

    public function getToNumber(): ?string
    {
        return $this->toNumber;
    }

    public function setToNumber(string $toNumber): self
    {
        $this->toNumber = $toNumber;

        return $this;
    }

    public function getSaleId(): ?int
    {
        return $this->saleId;
    }

    public function setSaleId(?int $saleId): self
    {
        $this->saleId = $saleId;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }


}
