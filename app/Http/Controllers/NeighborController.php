<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NeighborController extends Controller
{
    public function getNeighborsData()
    {
        $user = Auth::user();
        
        // Fetch current meta neighbors
        $currentNeighborsMeta = DB::table('playermeta')
            ->where('uid', $user->uid)
            ->where('meta_key', 'current_neighbors')
            ->value('meta_value');
        
        $neighborIds = $currentNeighborsMeta ? unserialize($currentNeighborsMeta) : [];
        
        $neighbors = [];
        
        if (!empty($neighborIds)) {
            // Fetch neighbor data
            $neighborsQuery = DB::table('users as u')
                ->join('usermeta as um', 'u.uid', '=', 'um.uid')
                ->join('useravatars as ua', 'u.uid', '=', 'ua.uid')
                ->whereIn('u.uid', $neighborIds)
                ->select(
                    'u.uid',
                    'u.name',
                    'um.firstName',
                    'um.lastName',
                    'ua.value as avatar_value'
                )
                ->get();
            
            // Group data by neighbor
            $groupedNeighbors = [];
            foreach ($neighborsQuery as $row) {
                $avatarData = @unserialize($row->avatar_value, ['allowed_classes' => false]);
                $gender = is_array($avatarData) && isset($avatarData['gender'])
                    ? $avatarData['gender']
                    : 'male';
                
                if (!isset($groupedNeighbors[$row->uid])) {
                    $groupedNeighbors[$row->uid] = [
                        'uid' => $row->uid,
                        'name' => $row->name,
                        'first_name' => $row->firstName,
                        'last_name' => $row->lastName,
                        'sex' => $gender == 'male' ? 'm' : 'f',
                    ];
                }
            }
            
            // Format to FarmVille standard
            foreach ($groupedNeighbors as $neighbor) {
                $neighbors[] = [
                    'uid' => $neighbor['uid'],
                    'first_name' => $neighbor['first_name'],
                    'last_name' => $neighbor['last_name'],
                    'name' => $neighbor['name'],
                    'pic_square' => '',
                    'sex' => $neighbor['sex'],
                    '___is_app_user' => true,
                    '___allowed_restrictions' => false,
                    '___pic_big' => ''
                ];
            }
        }
        
        return [
            'neighbors' => $neighbors,
            'neighborIds' => array_column($neighbors, 'uid'),
            'neighborsBase64' => base64_encode(json_encode($neighbors))
        ];
    }
    
    public function addNeighbor(Request $request)
    {
        $user = Auth::user();
        $neighborId = $request->input('neighbor_id');
        
        // Check if neighbor exists
        $neighborExists = DB::table('users')->where('uid', $neighborId)->exists();
        
        if (!$neighborExists) {
            return response()->json(['error' => 'Neighbor not found'], 404);
        }
        
        // Fetch current neighbors
        $currentNeighborsMeta = DB::table('playermeta')
            ->where('uid', $user->uid)
            ->where('meta_key', 'current_neighbors')
            ->value('meta_value');
        
        $neighborIds = $currentNeighborsMeta ? unserialize($currentNeighborsMeta) : [];
        
        // Add new neighbor if doesn't exist
        if (!in_array($neighborId, $neighborIds)) {
            $neighborIds[] = $neighborId;
            
            // Check if record already exists
            $exists = DB::table('playermeta')
                ->where('uid', $user->uid)
                ->where('meta_key', 'current_neighbors')
                ->exists();
            
            if ($exists) {
                DB::table('playermeta')
                    ->where('uid', $user->uid)
                    ->where('meta_key', 'current_neighbors')
                    ->update(['meta_value' => serialize($neighborIds)]);
            } else {
                DB::table('playermeta')->insert([
                    'uid' => $user->uid,
                    'meta_key' => 'current_neighbors',
                    'meta_value' => serialize($neighborIds)
                ]);
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Neighbor added successfully']);
    }
    
    public function removeNeighbor(Request $request)
    {
        $user = Auth::user();
        $neighborId = $request->input('neighbor_id');
        
        // Fetch current neighbors
        $currentNeighborsMeta = DB::table('playermeta')
            ->where('uid', $user->uid)
            ->where('meta_key', 'current_neighbors')
            ->value('meta_value');
        
        $neighborIds = $currentNeighborsMeta ? unserialize($currentNeighborsMeta) : [];
        
        // Remove neighbor
        $neighborIds = array_values(array_filter($neighborIds, function($id) use ($neighborId) {
            return $id != $neighborId;
        }));
        
        if (empty($neighborIds)) {
        // If empty, delete record
            DB::table('playermeta')
                ->where('uid', $user->uid)
                ->where('meta_key', 'current_neighbors')
                ->delete();
        } else {
            // Otherwise, just update
            DB::table('playermeta')
                ->where('uid', $user->uid)
                ->where('meta_key', 'current_neighbors')
                ->update(['meta_value' => serialize($neighborIds)]);
        }

        return response()->json(['success' => true, 'message' => 'Neighbor removed successfully']);
    }
    
    public function getPotentialNeighbors()
    {
        $user = Auth::user();
        
        // Fetch current neighbors
        $currentNeighborsMeta = DB::table('playermeta')
            ->where('uid', $user->uid)
            ->where('meta_key', 'current_neighbors')
            ->value('meta_value');
        
        $currentNeighborIds = $currentNeighborsMeta ? unserialize($currentNeighborsMeta) : [];
        $currentNeighborIds[] = $user->uid; // Exclude the user itself
        
        // Fetch users who are not neighbors
        $potentialNeighbors = DB::table('users as u')
            ->join('usermeta as um', 'u.uid', '=', 'um.uid')
            ->join('useravatars as ua', 'u.uid', '=', 'ua.uid')
            ->whereNotIn('u.uid', $currentNeighborIds)
            ->select(
                'u.uid',
                'u.name',
                'um.firstName',
                'um.lastName',
                'ua.value as avatar_value'
            )
            ->get();
        
        // Group data
        $groupedUsers = [];
        foreach ($potentialNeighbors as $row) {
            $avatarData = @unserialize($row->avatar_value, ['allowed_classes' => false]);
            $gender = is_array($avatarData) && isset($avatarData['gender'])
                ? $avatarData['gender']
                : 'f';

            if (!isset($groupedUsers[$row->uid])) {
                $groupedUsers[$row->uid] = [
                    'uid' => $row->uid,
                    'name' => $row->name,
                    'first_name' => $row->firstName,
                    'last_name' => $row->lastName,
                    'sex' => $gender,
                ];
            }
        }
        
        return response()->json(['users' => array_values($groupedUsers)]);
    }

    public function getPendingRequests()
    {
        $user = Auth::user();
        
        // Fetch pending requests
        $pendingNeighborsMeta = DB::table('playermeta')
            ->where('uid', $user->uid)
            ->where('meta_key', 'pending_neighbors')
            ->value('meta_value');
        
        $pendingIds = $pendingNeighborsMeta ? unserialize($pendingNeighborsMeta) : [];
        
        $pendingNeighbors = [];
        
        if (!empty($pendingIds)) {
            $neighborsQuery = DB::table('users as u')
                ->join('usermeta as um', 'u.uid', '=', 'um.uid')
                ->join('useravatars as ua', 'u.uid', '=', 'ua.uid')
                ->whereIn('u.uid', $pendingIds)
                ->select(
                    'u.uid',
                    'u.name',
                    'um.firstName',
                    'um.lastName',
                    'ua.value as avatar_value'
                )
                ->get();
            
            $groupedNeighbors = [];
            foreach ($neighborsQuery as $row) {
                $avatarData = @unserialize($row->avatar_value, ['allowed_classes' => false]);
                $gender = is_array($avatarData) && isset($avatarData['gender'])
                    ? $avatarData['gender']
                    : 'male';
                    
                if (!isset($groupedNeighbors[$row->uid])) {
                    $groupedNeighbors[$row->uid] = [
                        'uid' => $row->uid,
                        'name' => $row->name,
                        'first_name' => $row->firstName,
                        'last_name' => $row->lastName,
                        'sex' => $gender == 'male' ? 'm' : 'f',
                    ];
                }
            }
            
            $pendingNeighbors = array_values($groupedNeighbors);
        }
        
        return response()->json([
            'pending' => $pendingNeighbors,
            'count' => count($pendingNeighbors)
        ]);
    }

    public function acceptNeighbor(Request $request)
    {
        $validated = $request->validate([
            'neighbor_id' => 'required|string|max:50'
        ]);
        
        $neighborId = $validated['neighbor_id'];
        $user = Auth::user();
        
        // Remove from pending requests
        $pendingMeta = DB::table('playermeta')
            ->where('uid', $user->uid)
            ->where('meta_key', 'pending_neighbors')
            ->value('meta_value');
        
        $pendingIds = $pendingMeta ? unserialize($pendingMeta) : [];

        $pendingIds = array_values(array_filter($pendingIds, function($id) use ($neighborId) {
            return $id != $neighborId;
        }));

        if (empty($pendingIds)) {
            // If no pending left, delete record
            DB::table('playermeta')
                ->where('uid', $user->uid)
                ->where('meta_key', 'pending_neighbors')
                ->delete();
        } else {
            // Otherwise, just update
            DB::table('playermeta')
                ->where('uid', $user->uid)
                ->where('meta_key', 'pending_neighbors')
                ->update(['meta_value' => serialize($pendingIds)]);
        }
        
        // Add to current neighbors
        $currentMeta = DB::table('playermeta')
            ->where('uid', $user->uid)
            ->where('meta_key', 'current_neighbors')
            ->value('meta_value');
        
        $currentIds = $currentMeta ? unserialize($currentMeta) : [];
        
        if (!in_array($neighborId, $currentIds)) {
            $currentIds[] = $neighborId;
            
            $exists = DB::table('playermeta')
                ->where('uid', $user->uid)
                ->where('meta_key', 'current_neighbors')
                ->exists();
            
            if ($exists) {
                DB::table('playermeta')
                    ->where('uid', $user->uid)
                    ->where('meta_key', 'current_neighbors')
                    ->update(['meta_value' => serialize($currentIds)]);
            } else {
                DB::table('playermeta')->insert([
                    'uid' => $user->uid,
                    'meta_key' => 'current_neighbors',
                    'meta_value' => serialize($currentIds)
                ]);
            }
        }
        
        // Add current user as neighbor of the other user as well
        $neighborCurrentMeta = DB::table('playermeta')
            ->where('uid', $neighborId)
            ->where('meta_key', 'current_neighbors')
            ->value('meta_value');
        
        $neighborCurrentIds = $neighborCurrentMeta ? unserialize($neighborCurrentMeta) : [];
        
        if (!in_array($user->uid, $neighborCurrentIds)) {
            $neighborCurrentIds[] = $user->uid;
            
            $neighborExists = DB::table('playermeta')
                ->where('uid', $neighborId)
                ->where('meta_key', 'current_neighbors')
                ->exists();
            
            if ($neighborExists) {
                DB::table('playermeta')
                    ->where('uid', $neighborId)
                    ->where('meta_key', 'current_neighbors')
                    ->update(['meta_value' => serialize($neighborCurrentIds)]);
            } else {
                DB::table('playermeta')->insert([
                    'uid' => $neighborId,
                    'meta_key' => 'current_neighbors',
                    'meta_value' => serialize($neighborCurrentIds)
                ]);
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Neighbor request accepted successfully']);
    }

    public function rejectNeighbor(Request $request)
    {
        $user = Auth::user();
        $neighborId = $request->input('neighbor_id');
        
        // Fetch pending requests list
        $pendingMeta = DB::table('playermeta')
            ->where('uid', $user->uid)
            ->where('meta_key', 'pending_neighbors')
            ->value('meta_value');
        
        $pendingIds = $pendingMeta ? unserialize($pendingMeta) : [];

        // Remove rejected neighbor
        $pendingIds = array_values(array_filter($pendingIds, function($id) use ($neighborId) {
            return $id != $neighborId;
        }));

        if (empty($pendingIds)) {
            // If no pending left, delete record
            DB::table('playermeta')
                ->where('uid', $user->uid)
                ->where('meta_key', 'pending_neighbors')
                ->delete();
        } else {
            // Otherwise, just update
            DB::table('playermeta')
                ->where('uid', $user->uid)
                ->where('meta_key', 'pending_neighbors')
                ->update(['meta_value' => serialize($pendingIds)]);
        }

        return response()->json(['success' => true, 'message' => 'Neighbor request rejected successfully']);
    }

    public function sendNeighborRequest(Request $request)
    {
        $user = Auth::user();
        $neighborId = $request->input('neighbor_id');
        
        // Check if neighbor exists
        $neighborExists = DB::table('users')->where('uid', $neighborId)->exists();
        
        if (!$neighborExists) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Add to recipient's pending list
        $pendingMeta = DB::table('playermeta')
            ->where('uid', $neighborId)
            ->where('meta_key', 'pending_neighbors')
            ->value('meta_value');
        
        $pendingIds = $pendingMeta ? unserialize($pendingMeta) : [];
        
        if (!in_array($user->uid, $pendingIds)) {
            $pendingIds[] = $user->uid;
            
            $exists = DB::table('playermeta')
                ->where('uid', $neighborId)
                ->where('meta_key', 'pending_neighbors')
                ->exists();
            
            if ($exists) {
                DB::table('playermeta')
                    ->where('uid', $neighborId)
                    ->where('meta_key', 'pending_neighbors')
                    ->update(['meta_value' => serialize($pendingIds)]);
            } else {
                DB::table('playermeta')->insert([
                    'uid' => $neighborId,
                    'meta_key' => 'pending_neighbors',
                    'meta_value' => serialize($pendingIds)
                ]);
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Neighbor request sent successfully']);
    }
}