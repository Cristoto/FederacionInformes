public class Principal{
    public static void Main (String[] args){
        
        int[] puntos = {20, 18, 16, 14, 13, 12, 11, 10, 8, 7, 6, 5, 4, 3, 2, 1};
        Deportista[] deps = {new Deportista(), new Deportista()};
        /*
        Deportista
            int punto
            boolean merecePunto
        */
        boolean bloquea;
        int indicePuntos = 0;


        for (int i = 0; i < deps.length(); i++) {
            
            if(indicePuntos > puntos.length()){
                
                deps[i].punto = 0;
            
            }else{

                if(bloquea){

                    if(deps[i].merecePunto){
                        deps[i].punto = puntos[indicePuntos];
                    }else{
                        deps[i].punto = 0;
                    }
                    indicePuntos++;
                }else{

                    if(deps[i].merecePunto){
                        deps[i].punto = puntos[indicePuntos];
                        indicePuntos++;
                    }else{
                        deps[i] = 0;
                    }
                }
            }
        }
    }
}
