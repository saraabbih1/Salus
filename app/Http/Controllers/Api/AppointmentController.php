<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class AppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/appointments",
     *     operationId="listAppointments",
     *     tags={"Appointments"},
     *     summary="List the authenticated user's appointments",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Appointments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="doctor_id", type="integer", example=2),
     *                     @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-03-30T14:30:00.000000Z"),
     *                     @OA\Property(property="status", type="string", example="confirmed"),
     *                     @OA\Property(property="notes", type="string", example="Follow-up consultation.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $appointments = Appointment::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $appointments,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/appointments",
     *     operationId="storeAppointment",
     *     tags={"Appointments"},
     *     summary="Create a new appointment",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"doctor_id","appointment_date"},
     *             @OA\Property(property="doctor_id", type="integer", example=2),
     *             @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-03-30 14:30:00"),
     *             @OA\Property(property="status", type="string", enum={"pending","confirmed","cancelled"}, example="pending"),
     *             @OA\Property(property="notes", type="string", example="First consultation for recurring headaches.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Appointment created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=2),
     *                 @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-03-30T14:30:00.000000Z"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="notes", type="string", example="First consultation for recurring headaches.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
        ]);

        $appointment = Appointment::create([
            'user_id' => $request->user()->id,
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'status' => $validated['status'] ?? 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully.',
            'data' => $appointment,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/appointments/{id}",
     *     operationId="showAppointment",
     *     tags={"Appointments"},
     *     summary="Get a single appointment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Appointment ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=2),
     *                 @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-03-30T14:30:00.000000Z"),
     *                 @OA\Property(property="status", type="string", example="confirmed"),
     *                 @OA\Property(property="notes", type="string", example="Follow-up consultation.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Appointment not found."))
     *     )
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::where('user_id', $request->user()->id)->find($id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $appointment,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/appointments/{id}",
     *     operationId="updateAppointment",
     *     tags={"Appointments"},
     *     summary="Update an appointment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Appointment ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="doctor_id", type="integer", example=2),
     *             @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-04-01 09:00:00"),
     *             @OA\Property(property="status", type="string", enum={"pending","confirmed","cancelled"}, example="confirmed"),
     *             @OA\Property(property="notes", type="string", example="Rescheduled after doctor confirmation.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Appointment updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=2),
     *                 @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-04-01T09:00:00.000000Z"),
     *                 @OA\Property(property="status", type="string", example="confirmed"),
     *                 @OA\Property(property="notes", type="string", example="Rescheduled after doctor confirmation.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Appointment not found."))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::where('user_id', $request->user()->id)->find($id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        $validated = $request->validate([
            'doctor_id' => ['sometimes', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['sometimes', 'date'],
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
        ]);

        $appointment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully.',
            'data' => $appointment->fresh(),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/appointments/{id}",
     *     operationId="deleteAppointment",
     *     tags={"Appointments"},
     *     summary="Delete an appointment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Appointment ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Appointment deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Appointment not found."))
     *     )
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::where('user_id', $request->user()->id)->find($id);

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully.',
        ]);
    }
}
