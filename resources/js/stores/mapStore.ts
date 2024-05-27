import { useLocalStorage } from "@vueuse/core"



type Marker={
    latitude: number;
    longitude: number;
};

export const userMarker = useLocalStorage<Marker>('USER_MARKER', {
    latitude: 46.519962,
    longitude: 6.633597,
});

export const nearbyMarkers = useLocalStorage<Marker[]>('NEARBY_MARKERS', []);
