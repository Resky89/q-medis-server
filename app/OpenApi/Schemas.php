<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="SuccessTokenResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="login success"),
 *   @OA\Property(
 *     property="data",
 *     type="object",
 *     @OA\Property(property="access_token", type="string"),
 *     @OA\Property(property="refresh_token", type="string")
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="SuccessUserResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="profile retrieved"),
 *   @OA\Property(property="data", ref="#/components/schemas/User")
 * )
 *
 * @OA\Schema(
 *   schema="SuccessEmptyResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="logged out"),
 *   @OA\Property(property="data", type="object", nullable=true, example=null)
 * )
 *
 * @OA\Schema(
 *   schema="ErrorResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="error"),
 *   @OA\Property(property="message", type="string", example="invalid credentials"),
 *   @OA\Property(property="errors", type="object")
 * )
 *
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Admin"),
 *   @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
 *   @OA\Property(property="role", type="string", nullable=true, example="admin"),
 *   @OA\Property(property="avatar", type="string", nullable=true, example="https://www.gravatar.com/avatar/...") ,
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="Loket",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="nama_loket", type="string", example="Loket A"),
 *   @OA\Property(property="kode_prefix", type="string", example="A"),
 *   @OA\Property(property="deskripsi", type="string", nullable=true, example="Pendaftaran"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="Antrian",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=10),
 *   @OA\Property(property="loket_id", type="integer", example=1),
 *   @OA\Property(property="nomor_antrian", type="string", example="A-0012"),
 *   @OA\Property(property="status", type="string", example="dipanggil"),
 *   @OA\Property(property="waktu_panggil", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="SuccessLoketListResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="lokets retrieved"),
 *   @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Loket")
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="SuccessDisplayResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="display retrieved"),
 *   @OA\Property(
 *     property="data",
 *     type="object",
 *     @OA\Property(property="loket", ref="#/components/schemas/Loket"),
 *     @OA\Property(property="current", ref="#/components/schemas/Antrian", nullable=true),
 *     @OA\Property(property="next", ref="#/components/schemas/Antrian", nullable=true)
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="Pagination",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="per_page", type="integer", example=15),
 *   @OA\Property(property="total", type="integer", example=42),
 *   @OA\Property(property="last_page", type="integer", example=3)
 * )
 *
 * @OA\Schema(
 *   schema="SuccessLoketPaginatedResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="lokets retrieved"),
 *   @OA\Property(
 *     property="data",
 *     type="object",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Loket")),
 *     @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="SuccessAntrianPaginatedResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="antrians retrieved"),
 *   @OA\Property(
 *     property="data",
 *     type="object",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Antrian")),
 *     @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="SuccessLoketResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="loket retrieved"),
 *   @OA\Property(property="data", ref="#/components/schemas/Loket")
 * )
 *
 * @OA\Schema(
 *   schema="SuccessAntrianResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="success"),
 *   @OA\Property(property="message", type="string", example="antrian retrieved"),
 *   @OA\Property(property="data", ref="#/components/schemas/Antrian")
 * )
 *
 * @OA\Schema(
 *   schema="LoketCreateRequest",
 *   type="object",
 *   required={"nama_loket","kode_prefix"},
 *   @OA\Property(property="nama_loket", type="string", maxLength=100),
 *   @OA\Property(property="kode_prefix", type="string", maxLength=5),
 *   @OA\Property(property="deskripsi", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="LoketUpdateRequest",
 *   type="object",
 *   @OA\Property(property="nama_loket", type="string", maxLength=100),
 *   @OA\Property(property="kode_prefix", type="string", maxLength=5),
 *   @OA\Property(property="deskripsi", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="AntrianCreateRequest",
 *   type="object",
 *   required={"loket_id"},
 *   @OA\Property(property="loket_id", type="integer")
 * )
 *
 * @OA\Schema(
 *   schema="AntrianUpdateRequest",
 *   type="object",
 *   required={"status"},
 *   @OA\Property(property="status", type="string", example="dipanggil")
 * )
 */
class Schemas
{
}
