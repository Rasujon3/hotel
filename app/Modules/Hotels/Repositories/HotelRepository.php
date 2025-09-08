<?php

namespace App\Modules\Hotels\Repositories;

use App\Helpers\ActivityLogger;
use App\Modules\Areas\Models\Area;
use App\Modules\Areas\Models\AreaHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class HotelRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $areas = Area::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $areas->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $areas->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $areas->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $areas->whereNotNull('deleted_at')->count();
        $totalUpdated = $areas->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalAreas = $areas->count();

        return [
            'totalAreas' => $totalAreas,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }
    public function list($request)
    {
        $query = Area::withTrashed()
            ->leftJoin('countries', 'countries.id', '=', 'areas.country_id')
            ->leftJoin('states', 'states.id', '=', 'areas.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'areas.city_id')
            ->select(
                'areas.*',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name'
            );
        if ($request->has('draft')) {
            $query->where('areas.draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('areas.is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('areas.is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('areas.deleted_at');
            } else {
                $query->whereNull('areas.deleted_at');
            }
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('areas.updated_at');
            } else {
                $query->whereNull('areas.updated_at');
            }
        }
        if ($request->has('country_id')) {
            $query->where('areas.country_id', $request->input('country_id'));
        }
        if ($request->has('state_id')) {
            $query->where('areas.state_id', $request->input('state_id'));
        }
        if ($request->has('city_id')) {
            $query->where('areas.city_id', $request->input('city_id'));
        }

        $list = $query->get();
        return $list;
    }
    public function store(array $data): ?Area
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if (isset($data['draft'])) {
                if($data['draft'] == 1) {
                    $data['drafted_at'] = now();
                    $data['is_active'] = 0;
                } else {
                    $data['is_active'] = 1;
                }
            } else {
                $data['is_active'] = 1;
            }

            // Create the Area record in the database
            $area = Area::create($data);

            $this->areaHistoryCreate('Area Add');

            DB::commit();

            return $area;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing Area: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Area $area, array $data): ?Area
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if (isset($data['draft'])) {
                if($data['draft'] == 1) {
                    $data['drafted_at'] = now();
                    $data['is_active'] = 0;
                } else {
                    $data['is_active'] = 1;
                }
            } else {
                $data['is_active'] = 1;
            }

            // Perform the update
            $area->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (isset($data['is_delete'])) {
                if ($data['is_delete'] == 1) {
                    $this->delete($area);
                } else {
                    $area->update([
                        'is_deleted' => 0,
                        'deleted_at' => null,
                        'is_active' => 1,
                        'draft' => 0
                    ]);
                    $this->areaHistoryCreate('Area Updated');
                }
            } else {
                $this->areaHistoryCreate('Area Updated');
            }

            DB::commit();
            return $area;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating Area: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(Area $area): bool
    {
        DB::beginTransaction();
        try {
            $area->update([
                'is_deleted' => 1,
                'is_active' => 0,
                'draft' => 1,
                'drafted_at' => now()
            ]);
            // Perform soft delete
            $deleted = $area->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }

            $this->areaHistoryCreate('Area Deleted');

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting Area: ' , [
                'state_id' => $area->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function find($id)
    {
        return Area::withTrashed()->find($id);
    }
    public function getData($id)
    {
        $area = Area::withTrashed()
            ->leftJoin('countries', 'countries.id', '=', 'areas.country_id')
            ->leftJoin('states', 'states.id', '=', 'areas.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'areas.city_id')
            ->where('areas.id', $id)
            ->select(
                'areas.*',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name'
            )
            ->first();
        return $area;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->areas as $data) {
                $area = Area::find($data['id']);

                if (!$area) {
                    continue; // Skip if city not found
                }

                // Update state details
                $area->update([
                    'code' => $data['code'] ?? $area->code,
                    'name' => $data['name'] ?? $area->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $area->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $area->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $area->is_default,
                    'draft' => $data['draft'] ?? $area->draft,
                    'drafted_at' => (isset($data['draft']) && $data['draft'] == 1) ? now() : $area->drafted_at,
                    'is_active' => ((isset($data['draft']) && $data['draft'] == 1)) ? 0 : 1,
                    'country_id' => $data['country_id'] ?? $area->country_id,
                    'state_id' => $data['state_id'] ?? $area->state_id,
                    'city_id' => $data['city_id'] ?? $area->city_id,
                ]);
                $this->areaHistoryCreate('Area Updated');
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating Area: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    private function areaHistoryCreate(string $actionType, $exportPdf = false, $exportXls = false, $exportPrint = false): bool
    {
        DB::beginTransaction();
        try {
            // Get the authenticated user
            $user = Auth::user();

            AreaHistory::create([
                'client_id' => $user->admin_client_id ?? null,
                'action_date' => now(),
                'action_by' => $user->name ?? null,
                'action_type' => $actionType,
                'export_pdf' => $exportPdf,
                'export_xls' => $exportXls,
                'export_print' => $exportPrint,
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error creating area history', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function checkAvailability(array $data): bool
    {
        try {
            $code = $data['code'] ?? null;
            $name = $data['name'] ?? null;

            // Check if either code or name already exists
            $exists = Area::where(function ($query) use ($code, $name) {
                if ($code) {
                    $query->orWhere('code', $code);
                }
                if ($name) {
                    $query->orWhere('name', $name);
                }
            })
                ->exists();

            // Return true if no match (available), false if exists (not available)
            return $exists;
        } catch (\Exception $e) {
            Log::error('Error checking area availability', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Default to false (not available) on error
            return false;
        }
    }
    public function history()
    {
        try {
            $history = AreaHistory::orderBy('id', 'desc')->get();
            return $history;
        } catch (\Exception $e) {
            Log::error('Error checking area history', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Default to false (not available) on error
            return false;
        }
    }
    public function import($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->areas as $data) {
                // create data
                $area = Area::create([
                    'code' => isset($data['code']) ?? '',
                    'name' => isset($data['name']) ?? '',
                    'name_in_bangla' => isset($data['name_in_bangla']) ?? null,
                    'name_in_arabic' => isset($data['name_in_arabic']) ?? null,
                    'is_default' => isset($data['is_default']) ?? 0,
                    'draft' => !empty($data['draft']) ? 1 : 0,
                    'drafted_at' => (isset($data['draft']) && $data['draft'] == 1) ? now() : null,
                    'is_active' => !empty($data['is_active']) ? 1 : 0,
                    'country_id' => $data['country_id'] ?? null,
                    'state_id' => $data['state_id'] ?? null,
                    'city_id' => $data['city_id'] ?? null,
                ]);

                $this->areaHistoryCreate('Area Created');
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error bulk import area: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
