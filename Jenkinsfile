pipeline{
  agent{
    node{
      label 'ubuntu'
    }
  }
  stages{
    stage('verify tools'){
     steps{
       sh '''
        docker info
        docker version
        docker-compose version
       '''
     }  
    }
    stage('Start Container'){
      steps{
        sh '''
           docker-compose up -d
        '''
      }
    }
    stage('Dependency installation'){
      steps{
        sh '''
           docker-compose exec -T ci4_service sh -c "ls"
           docker-compose restart
        '''
      }
    }
    stage('Environment Setting Up'){
      steps{
        script{
          sh '''
            cp app/env app/.env
          '''
        }
      }
    }
    stage('Database migrate'){
      steps{
        sh '''
           docker-compose exec -T ci4_service sh -c "php spark migrate"
        '''
      }
    }
    stage('Database seed'){
      steps{
        sh '''
           sleep 2 
           docker-compose exec -T ci4_service sh -c "php spark migrate"
           docker-compose exec -T ci4_service sh -c "php spark db:seed Members"
           docker-compose exec -T ci4_service sh -c "php spark db:seed TodoLists"
           docker-compose up -d
        '''
      }
    }
    stage('Unit testing'){
      steps{
        sh '''
           docker-compose exec -T ci4_service sh -c "vendor/bin/phpunit"
        '''
      }
    }
   }
   post {
        always {
            // 清理，停止所有服務
            sh 'docker-compose down'
        }
    }
}
